<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhysicalProfileController extends Controller
{
    // C - Guardar por primera vez
    public function store(Request $request)
    {
        $request->validate([
            'weight' => 'required|numeric',
            'height' => 'required|integer',
            'birth_date' => 'required|date',
        ]);

        auth()->user()->physicalProfile()->create($request->all());

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
        $request->validate([
            'weight' => 'required|numeric',
            'height' => 'required|integer',
            'birth_date' => 'required|date',
        ]);

        $perfil = auth()->user()->physicalProfile;
        $perfil->update($request->all());

        return redirect()->route('dashboard');
    }

    // D - Eliminar el registro por completo
    public function destroy()
    {
        $perfil = auth()->user()->physicalProfile;
        
        if ($perfil) {
            $perfil->delete();
        }

        // Al eliminarse, el Middleware se activará automáticamente y lo mandará al formulario
        return redirect()->route('dashboard')->with('status', 'perfil-actualizado');
    }
}