<?php

namespace App\Http\Controllers;

use App\Models\PhysicalProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PhysicalProfileController extends Controller
{
    private function reglas(): array
    {
        return [
            'weight'             => ['required', 'numeric', 'between:20,400'],
            'height'             => ['required', 'integer', 'between:80,260'],
            'birth_date'         => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'gender'             => ['required', Rule::in(PhysicalProfile::GENEROS)],
            'activity_level'     => ['required', Rule::in(PhysicalProfile::NIVELES_ACTIVIDAD)],
            'medical_conditions' => ['nullable', 'string', 'max:1000'],
        ];
    }

    // C - Guardar por primera vez
    public function store(Request $request)
    {
        // validate() devuelve solo los campos validados: nadie puede inyectar user_id u otros campos
        $datos = $request->validate($this->reglas());

        // Si ya existía un perfil lo actualiza, así nunca se duplica
        auth()->user()->physicalProfile()->updateOrCreate([], $datos);

        return redirect()->route('dashboard');
    }

    // U - Mostrar el formulario de edición
    public function edit()
    {
        $perfil = auth()->user()->physicalProfile;

        // Si por alguna razón no tiene perfil, lo mandamos a crear
        if (!$perfil) {
            return redirect('/perfil-prueba');
        }

        return view('edit-profile', compact('perfil'));
    }

    // U - Guardar los cambios modificados
    public function update(Request $request)
    {
        $datos = $request->validate($this->reglas());

        $perfil = auth()->user()->physicalProfile;

        if (!$perfil) {
            return redirect('/perfil-prueba');
        }

        $perfil->update($datos);

        return redirect()->route('dashboard')->with('status', 'perfil-actualizado');
    }

    // D - Eliminar el registro por completo
    public function destroy()
    {
        $perfil = auth()->user()->physicalProfile;

        if ($perfil) {
            $perfil->delete();
        }

        // Al eliminarse, el Middleware se activará automáticamente y lo mandará al formulario
        return redirect()->route('dashboard');
    }
}
