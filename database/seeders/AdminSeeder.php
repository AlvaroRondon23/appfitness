<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Crea (o repara) el primer administrador.
 * Ejecutar: php artisan db:seed --class=AdminSeeder   (repetible)
 * Acceso local por defecto: admin@appfitness.test / password
 * Puedes cambiarlo con ADMIN_EMAIL y ADMIN_PASSWORD en el .env. Si la cuenta ya existe no se toca su contraseña.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrNew(['email' => env('ADMIN_EMAIL', 'admin@appfitness.test')]);

        if (! $user->exists) {
            $user->password = Hash::make(env('ADMIN_PASSWORD', 'password'));
        }

        $user->forceFill([
            'name'                => $user->name ?: 'Administrador',
            'role'                => User::ROL_ADMIN,
            'disabled_at'         => null,
            'email_verified_at'   => $user->email_verified_at ?? now(),
            'privacy_accepted_at' => $user->privacy_accepted_at ?? now(),
        ])->save();
    }
}
