<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cierra la sesión de las cuentas desactivadas por un administrador.
 * Va en el grupo 'web', así también expulsa a quien ya tenía la sesión abierta.
 */
class CuentaActiva
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->estaDesactivada()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $mensaje = 'Esta cuenta está desactivada. Contacta con el administrador.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $mensaje], 403);
            }

            return redirect()->route('login')->withErrors(['email' => $mensaje]);
        }

        return $next($request);
    }
}
