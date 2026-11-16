<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Elimina una cuenta con todos sus datos. Lo usan "Mi cuenta" y el panel de administración.
 */
class BorradoCuenta
{
    public function eliminar(User $user): void
    {
        DB::transaction(function () use ($user) {
            // Cierra también las sesiones abiertas en otros dispositivos (tabla sessions, sin clave foránea)
            if (config('session.driver') === 'database') {
                DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
            }

            // El perfil físico, las sesiones de entrenamiento y las consultas a la IA
            // se eliminan en cascada (claves foráneas con cascadeOnDelete)
            $user->delete();
        });
    }
}
