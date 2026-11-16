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
                if (!$request->routeIs('perfil.crear') && !$request->is('physical-profile')) {
                    // Un administrador sin perfil va a su panel (no está obligado a tener perfil físico)
                    return $user->esAdmin()
                        ? redirect()->route('admin.panel')
                        : redirect()->route('perfil.crear');
                }
            } else {
                // 3. Si SÍ tiene perfil, pero intenta entrar al formulario de creación, lo mandamos al dashboard
                if ($request->routeIs('perfil.crear')) {
                    return redirect()->route('dashboard');
                }
            }
        }

        return $next($request);
    }
}
