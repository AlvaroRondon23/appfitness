<?php

namespace Database\Seeders;

use App\Models\PhysicalProfile;
use App\Models\SesionEntrenamiento;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Usuario de demostración con perfil físico y ~3 semanas de sesiones.
 * Ejecutar: php artisan db:seed --class=DemoSeeder  (se puede repetir: no duplica nada)
 * Acceso: demo@appfitness.test / password
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrNew(['email' => 'demo@appfitness.test']);
        $user->forceFill([
            'name'              => 'Usuario Demo',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
        ])->save();

        $perfil = PhysicalProfile::firstOrNew(['user_id' => $user->id]);
        $perfil->forceFill([
            'weight'         => 72.5,
            'height'         => 175,
            'birth_date'     => '1995-06-15',
            'gender'         => 'Masculino',
            'activity_level' => 'Moderado',
        ])->save();

        // Se regeneran las sesiones del usuario demo para que el seeder sea repetible
        SesionEntrenamiento::where('user_id', $user->id)->delete();

        mt_srand(2026); // datos reproducibles
        $ejercicios = ['sentadilla', 'biceps', 'flexion', 'salto_tijera', 'elevacion_rodillas'];
        $dias = 21;

        for ($offset = $dias - 1; $offset >= 0; $offset--) {
            // Descansa 2 de cada 7 días
            if (in_array($offset % 7, [2, 5], true)) {
                continue;
            }

            $progreso = ($dias - 1 - $offset) / ($dias - 1); // 0 al inicio, 1 hoy
            $sesionesDelDia = mt_rand(1, 2);

            for ($k = 0; $k < $sesionesDelDia; $k++) {
                $ejercicio = $ejercicios[($offset + $k) % count($ejercicios)];
                $reps = (int) round(10 + $progreso * 8) + mt_rand(0, 4);
                $puntaje = min(100, (int) round(70 + $progreso * 20) + mt_rand(-5, 8));

                $hora = mt_rand(6, 20);
                if ($offset === 0) {
                    $hora = min($hora, now()->hour); // nada en el futuro
                }
                $fecha = Carbon::today()->subDays($offset)->setTime($hora, mt_rand(0, 59));

                $sesion = new SesionEntrenamiento([
                    'user_id'         => $user->id,
                    'ejercicio'       => $ejercicio,
                    'repeticiones'    => $reps,
                    'tiempo_segundos' => $reps * 4 + mt_rand(20, 60),
                    'puntaje'         => $puntaje,
                ]);
                $sesion->created_at = $fecha;
                $sesion->updated_at = $fecha;
                $sesion->save();
            }
        }
    }
}
