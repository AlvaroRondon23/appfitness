<?php

namespace App\Http\Controllers;

use App\Services\ContextoAtleta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AiAssistantController extends Controller
{
    // Tiempo máximo total (segundos) para toda la consulta, sumando modelos e intentos.
    // Debe quedar por debajo del max_execution_time de PHP, que se amplía abajo.
    private const PRESUPUESTO_SEGUNDOS = 45;

    // Motivos con los que Gemini corta o bloquea una respuesta por seguridad
    private const MOTIVOS_BLOQUEO = ['SAFETY', 'PROHIBITED_CONTENT', 'BLOCKLIST', 'SPII', 'RECITATION', 'OTHER'];

    public function index(): View
    {
        // Últimas 20 consultas del usuario, de la más antigua a la más reciente
        $historial = auth()->user()->consultasIa()
            ->latest('id')->take(20)->get()
            ->reverse()->values();

        return view('asistente', compact('historial'));
    }

    public function generateResponse(Request $request, ContextoAtleta $contexto): JsonResponse
    {
        $datos = $request->validate([
            'prompt' => ['required', 'string', 'max:1000'],
        ], [
            'prompt.required' => 'Escribe una consulta.',
            'prompt.max'      => 'La consulta es demasiado larga (máximo 1000 caracteres).',
        ]);

        $apiKey = config('services.gemini.key');

        if (!$apiKey) {
            Log::error('Falta GEMINI_API_KEY en el archivo .env');

            return $this->error(
                'El asistente no está disponible por el momento. Inténtalo más tarde.',
                500,
                'GEMINI_API_KEY no configurada en .env'
            );
        }

        // Ampliar el límite de PHP para que no corte la petición antes de responder
        @set_time_limit(self::PRESUPUESTO_SEGUNDOS + 15);
        $inicio = microtime(true);

        $user = $request->user();

        // Memoria de la conversación: las últimas 6 consultas como turnos previos
        $contents = [];
        foreach ($user->consultasIa()->latest('id')->take(6)->get()->reverse() as $previa) {
            $contents[] = ['role' => 'user',  'parts' => [['text' => $previa->pregunta]]];
            $contents[] = ['role' => 'model', 'parts' => [['text' => $previa->respuesta]]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $datos['prompt']]]];

        $payload = [
            // Datos del usuario y reglas de comportamiento
            'systemInstruction' => ['parts' => [['text' => $contexto->construir($user)]]],
            'contents'          => $contents,
        ];

        $sinConexion  = false;
        $ultimoStatus = null;
        $modelos      = config('services.gemini.models');

        foreach ($modelos as $modelo) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelo}:generateContent";

            // Hasta 2 intentos por modelo si el servicio está saturado (503) o limita (429)
            for ($intento = 1; $intento <= 2; $intento++) {
                $restante = self::PRESUPUESTO_SEGUNDOS - (microtime(true) - $inicio);

                if ($restante < 4) {
                    Log::warning('Presupuesto de tiempo agotado consultando a Gemini');
                    break 2;
                }

                // El timeout de cada petición nunca supera el tiempo que queda
                $timeout = (int) max(3, min((int) config('services.gemini.timeout'), floor($restante)));

                try {
                    $http = Http::withHeaders([
                            'x-goog-api-key' => $apiKey, // la clave va en cabecera, no en la URL
                            'Content-Type'   => 'application/json',
                        ])
                        ->timeout($timeout);

                    if (!config('services.gemini.verify_ssl')) {
                        $http = $http->withoutVerifying();
                    }

                    $response = $http->post($url, $payload);

                    if ($response->successful()) {
                        $json = $response->json() ?? [];
                        $texto = $this->extraerTexto($json);

                        if ($texto === '') {
                            $bloqueo = $json['promptFeedback']['blockReason'] ?? null;
                            $motivo  = $json['candidates'][0]['finishReason'] ?? null;

                            Log::warning('Gemini respondió sin texto', [
                                'modelo'       => $modelo,
                                'blockReason'  => $bloqueo,
                                'finishReason' => $motivo,
                            ]);

                            $mensaje = ($bloqueo !== null || in_array($motivo, self::MOTIVOS_BLOQUEO, true))
                                ? 'No puedo responder a esa consulta. Prueba a formularla centrada en tu entrenamiento, nutrición o descanso.'
                                : 'No pude generar una respuesta para esa consulta. Intenta reformularla.';

                            return $this->error($mensaje, 422);
                        }

                        $user->consultasIa()->create([
                            'pregunta'  => $datos['prompt'],
                            'respuesta' => $texto,
                            'modelo'    => $modelo,
                        ]);

                        return response()->json([
                            'respuesta' => $texto,
                            'html'      => $this->aHtml($texto),
                        ]);
                    }

                    $ultimoStatus = $response->status();
                    Log::warning('Error de la API de Gemini', [
                        'modelo' => $modelo,
                        'status' => $ultimoStatus,
                        'cuerpo' => Str::limit($response->body(), 500),
                    ]);

                    if (in_array($ultimoStatus, [429, 503], true)) {
                        // Solo se espera si habrá un segundo intento y queda tiempo
                        if ($intento < 2 && $restante > 8) {
                            sleep(1);
                        }
                        continue;
                    }

                    break; // Otro error (400, 403, 404...): pasa al siguiente modelo

                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    $sinConexion = true;
                    Log::warning('No se pudo conectar con Gemini', ['mensaje' => $e->getMessage()]);
                    break;
                } catch (\Throwable $e) {
                    Log::error('Error inesperado consultando a Gemini', ['mensaje' => $e->getMessage()]);
                    break;
                }
            }
        }

        if ($sinConexion) {
            return $this->error(
                'No se pudo conectar con el servicio de IA. Revisa tu conexión a internet e inténtalo de nuevo.',
                503
            );
        }

        if (in_array($ultimoStatus, [400, 401, 403], true)) {
            return $this->error(
                'El asistente no está disponible por el momento. Inténtalo más tarde.',
                503,
                "Gemini rechazó la solicitud (HTTP {$ultimoStatus}): revisa la clave y los modelos. Detalle en storage/logs/laravel.log"
            );
        }

        return $this->error(
            'El servicio de IA está experimentando una alta demanda en este momento. Por favor, intenta de nuevo en unos segundos.',
            503
        );
    }

    // Borra el historial de conversación guardado del usuario
    public function limpiar(Request $request): RedirectResponse
    {
        $request->user()->consultasIa()->delete();

        return redirect()->route('asistente');
    }

    // Une las partes de texto de la respuesta, ignorando los "pensamientos" internos del modelo
    private function extraerTexto(array $json): string
    {
        $partes = $json['candidates'][0]['content']['parts'] ?? [];

        return trim(collect($partes)
            ->reject(fn ($parte) => !empty($parte['thought']))
            ->pluck('text')
            ->filter()
            ->implode("\n"));
    }

    // Respuesta de error uniforme. El detalle técnico solo se muestra con APP_DEBUG=true.
    private function error(string $mensaje, int $status, ?string $detalleDebug = null): JsonResponse
    {
        if ($detalleDebug !== null && config('app.debug')) {
            $mensaje .= " [{$detalleDebug}]";
        }

        return response()->json([
            'respuesta' => $mensaje,
            'html'      => '<p>' . e($mensaje) . '</p>',
        ], $status);
    }

    // Convierte el markdown de la IA en HTML seguro (el HTML crudo se escapa)
    private function aHtml(string $texto): string
    {
        return Str::markdown($texto, [
            'html_input'         => 'escape',
            'allow_unsafe_links' => false,
        ]);
    }
}
