<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ejercicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminEjercicioController extends Controller
{
    public function index(): View
    {
        return view('admin.ejercicios.index', [
            'ejercicios' => Ejercicio::withCount('sesiones')->orderBy('id')->get(),
        ]);
    }

    /** Solo nombre y descripción: la lógica de cada ejercicio vive en el detector. */
    public function update(Request $request, Ejercicio $ejercicio): RedirectResponse
    {
        $datos = $request->validateWithBag('ejercicio_'.$ejercicio->id, [
            'nombre'      => ['required', 'string', 'max:60', Rule::unique('ejercicios', 'nombre')->ignore($ejercicio->id)],
            'descripcion' => ['nullable', 'string', 'max:300'],
        ]);

        $ejercicio->update([
            'nombre'      => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
        ]);

        return back()->with('status', 'Ejercicio actualizado.');
    }

    public function activo(Ejercicio $ejercicio): RedirectResponse
    {
        if ($ejercicio->activo && Ejercicio::activos()->count() <= 1) {
            return back()->with('error', 'Debe quedar al menos un ejercicio activo.');
        }

        $ejercicio->update(['activo' => ! $ejercicio->activo]);

        return back()->with('status', $ejercicio->activo ? 'Ejercicio activado.' : 'Ejercicio desactivado.');
    }
}
