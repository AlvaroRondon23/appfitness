<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiAssistantController extends Controller
{
    public function index()
    {
        return view('asistente');
    }

    public function consultar(Request $request)
    {
        return $this->procesarConsulta($request);
    }

    public function generateResponse(Request $request)
    {
        return $this->procesarConsulta($request);
    }

    private function procesarConsulta(Request $request)
    {
        $prompt = $request->input('prompt') ?? $request->input('mensaje');

        if (!$prompt) {
            return response()->json([
                'respuesta' => 'Por favor, ingresa una consulta válida.'
            ], 400);
        }

        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'respuesta' => 'Error: La API Key de Gemini no está configurada en el archivo .env.'
            ], 500);
        }

        // Lista de modelos a intentar en orden de preferencia si hay saturación (503)
        $modelos = [
            'gemini-3.6-flash',
            'gemini-1.5-flash',
            'gemini-2.5-flash',
        ];

        $ultimoError = '';

        foreach ($modelos as $modelo) {
            $url = "https://generativelanguage.googleapis.com/v1/models/{$modelo}:generateContent?key={$apiKey}";

            // Hasta 2 intentos por modelo si responde con 503
            for ($intento = 1; $intento <= 2; $intento++) {
                try {
                    $response = Http::withoutVerifying()
                        ->timeout(15)
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                        ])
                        ->post($url, [
                            'contents' => [
                                [
                                    'parts' => [
                                        ['text' => $prompt]
                                    ]
                                ]
                            ]
                        ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        $textoRespuesta = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No se recibió una respuesta válida.';
                        return response()->json(['respuesta' => $textoRespuesta]);
                    }

                    $status = $response->status();
                    $ultimoError = $response->body();

                    // Si es un error 503 (saturación), pausa 1 segundo y reintenta
                    if ($status === 503) {
                        sleep(1);
                        continue;
                    }

                    // Si es un error distinto a 503 (ej: 404 o 400), pasa directamente al siguiente modelo
                    break;

                } catch (\Exception $e) {
                    $ultimoError = $e->getMessage();
                }
            }
        }

        return response()->json([
            'respuesta' => 'El servicio de IA está experimentando una alta demanda en este momento. Por favor, intenta de nuevo en unos segundos.'
        ], 503);
    }
}