<?php

namespace App\Services;

use App\Models\SesionEntrenamiento;
use App\Models\User;

/**
 * Resumen del rendimiento de un usuario a partir de sus sesiones guardadas.
 */
class EstadisticasEntrenamiento
{
    private const DIAS_SEMANA = ['D', 'L', 'M', 'M', 'J', 'V', 'S'];

    public function resumen(User $user): array
    {
        $totales = SesionEntrenamiento::where('user_id', $user->id)
            ->selectRaw('COUNT(*) AS sesiones, COALESCE(SUM(repeticiones), 0) AS repeticiones, COALESCE(SUM(tiempo_segundos), 0) AS segundos, COALESCE(MAX(repeticiones), 0) AS mejor')
            ->first();

        $porEjercicio = SesionEntrenamiento::where('user_id', $user->id)
            ->selectRaw('ejercicio, COUNT(*) AS sesiones, SUM(repeticiones) AS repeticiones, MAX(repeticiones) AS mejor')
            ->groupBy('ejercicio')
            ->orderByDesc('repeticiones')
            ->get();

        // Repeticiones por día en los últimos 60 días (se agrupa en PHP para respetar la zona horaria de la app)
        $recientes = SesionEntrenamiento::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(59)->startOfDay())
            ->get(['repeticiones', 'created_at']);

        $repsPorDia = [];
        foreach ($recientes as $sesion) {
            $dia = $sesion->created_at->format('Y-m-d');
            $repsPorDia[$dia] = ($repsPorDia[$dia] ?? 0) + $sesion->repeticiones;
        }

        // Últimos 7 días (de más antiguo a hoy) para el gráfico
        $semana = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i);
            $semana[] = [
                'etiqueta' => self::DIAS_SEMANA[$fecha->dayOfWeek],
                'reps'     => $repsPorDia[$fecha->format('Y-m-d')] ?? 0,
            ];
        }

        // Racha: días consecutivos con entrenamiento (si hoy aún no entrenó, cuenta desde ayer)
        $racha = 0;
        $cursor = now()->startOfDay();
        if (!isset($repsPorDia[$cursor->format('Y-m-d')])) {
            $cursor->subDay();
        }
        while (isset($repsPorDia[$cursor->format('Y-m-d')])) {
            $racha++;
            $cursor->subDay();
        }

        $desde30 = now()->subDays(29)->format('Y-m-d');
        $diasActivos30 = count(array_filter(
            array_keys($repsPorDia),
            fn ($dia) => $dia >= $desde30
        ));

        return [
            'sesiones'      => (int) $totales->sesiones,
            'repeticiones'  => (int) $totales->repeticiones,
            'segundos'      => (int) $totales->segundos,
            'mejor'         => (int) $totales->mejor,
            'por_ejercicio' => $porEjercicio,
            'semana'        => $semana,
            'max_semana'    => max(array_column($semana, 'reps')),
            'racha'         => $racha,
            'dias_activos'  => $diasActivos30,
        ];
    }
}
