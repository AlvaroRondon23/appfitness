<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPhysicalProfile
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificamos si el usuario ya inició sesión
        if (Auth::check()) {
            $user = Auth::user();

            // 2. Si el usuario NO tiene un perfil físico creado en la base de datos...
            if (!$user->physicalProfile) {
                // ... y si no está intentando entrar ya al formulario o guardarlo, lo redirigimos
                if (!$request->is('perfil-prueba') && !$request->is('physical-profile')) {
                    return redirect('/perfil-prueba');
                }
            } else {
                // 3. Si SÍ tiene perfil, pero intenta entrar al formulario viejo, lo mandamos al dashboard
                if ($request->is('perfil-prueba')) {
                    return redirect()->route('dashboard');
                }
            }
        }

        return $next($request);
    }
}