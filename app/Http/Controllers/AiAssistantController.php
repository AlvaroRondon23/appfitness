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
            return response()->json([
                'respuesta' => 'Error: la API Key de Gemini no está configurada en el archivo .env (GEMINI_API_KEY).',
            ], 500);
        }

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

        $sinConexion = false;
        $ultimoStatus = null;

        foreach (config('services.gemini.models') as $modelo) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelo}:generateContent";

            // Hasta 2 intentos por modelo si el servicio está saturado (503) o limita (429)
            for ($intento = 1; $intento <= 2; $intento++) {
                try {
                    $http = Http::withHeaders([
                            'x-goog-api-key' => $apiKey, // la clave va en cabecera, no en la URL
                            'Content-Type'   => 'application/json',
                        ])
                        ->timeout(config('services.gemini.timeout'));

                    if (!config('services.gemini.verify_ssl')) {
                        $http = $http->withoutVerifying();
                    }

                    $response = $http->post($url, $payload);

                    if ($response->successful()) {
                        $partes = $response->json('candidates.0.content.parts') ?? [];
                        $texto = trim(collect($partes)->pluck('text')->filter()->implode("\n"));

                        if ($texto === '') {
                            Log::warning('Gemini respondió sin texto', ['modelo' => $modelo]);
                            return response()->json([
                                'respuesta' => 'No pude generar una respuesta para esa consulta. Intenta reformularla.',
                                'html'      => '<p>No pude generar una respuesta para esa consulta. Intenta reformularla.</p>',
                            ], 422);
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
                        sleep(1);
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
            $mensaje = 'No se pudo conectar con el servicio de IA. Revisa tu conexión a internet e inténtalo de nuevo.';
        } elseif (in_array($ultimoStatus, [400, 401, 403], true)) {
            $mensaje = 'El servicio de IA rechazó la solicitud (clave o modelo no válidos). Revisa storage/logs/laravel.log.';
        } else {
            $mensaje = 'El servicio de IA está experimentando una alta demanda en este momento. Por favor, intenta de nuevo en unos segundos.';
        }

        return response()->json(['respuesta' => $mensaje, 'html' => '<p>' . e($mensaje) . '</p>'], 503);
    }

    // Borra el historial de conversación guardado del usuario
    public function limpiar(Request $request): RedirectResponse
    {
        $request->user()->consultasIa()->delete();

        return redirect()->route('asistente');
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
