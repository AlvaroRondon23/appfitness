<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SesionEntrenamientoController extends Controller
{
    /**
     * Guarda una sesión de entrenamiento del usuario autenticado.
     */
    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            // Debe existir en el catálogo (tabla ejercicios)
            'ejercicio'       => ['required', 'string', Rule::exists('ejercicios', 'clave')],
            'repeticiones'    => ['required', 'integer', 'min:1', 'max:1000'],
            'tiempo_segundos' => ['required', 'integer', 'min:0', 'max:86400'],
        ]);

        // El user_id se toma de la sesión, nunca del navegador
        $sesion = $request->user()->sesiones()->create($datos);

        return response()->json(['ok' => true, 'id' => $sesion->id], 201);
    }
}
