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

    // Meta semanal de sesiones (la semana va de lunes a domingo)
    private const META_SEMANAL = 3;

    public function resumen(User $user): array
    {
        $totales = SesionEntrenamiento::where('user_id', $user->id)
            ->selectRaw('COUNT(*) AS sesiones, COALESCE(SUM(repeticiones), 0) AS repeticiones, COALESCE(SUM(tiempo_segundos), 0) AS segundos, COALESCE(MAX(repeticiones), 0) AS mejor, AVG(puntaje) AS puntaje_promedio')
            ->first();

        $porEjercicio = SesionEntrenamiento::where('user_id', $user->id)
            ->selectRaw('ejercicio, COUNT(*) AS sesiones, SUM(repeticiones) AS repeticiones, MAX(repeticiones) AS mejor, ROUND(AVG(puntaje)) AS puntaje')
            ->groupBy('ejercicio')
            ->orderByDesc('repeticiones')
            ->get();

        // Sesiones de los últimos 60 días (se agrupa en PHP para respetar la zona horaria de la app)
        $recientes = SesionEntrenamiento::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(59)->startOfDay())
            ->get(['repeticiones', 'puntaje', 'created_at']);

        $inicioSemana = now()->startOfWeek(); // lunes

        $repsPorDia = [];
        $puntajesPorDia = [];
        $sesionesSemana = 0;
        foreach ($recientes as $sesion) {
            $dia = $sesion->created_at->format('Y-m-d');
            $repsPorDia[$dia] = ($repsPorDia[$dia] ?? 0) + $sesion->repeticiones;

            if ($sesion->puntaje !== null) {
                $puntajesPorDia[$dia][] = $sesion->puntaje;
            }
            if ($sesion->created_at->greaterThanOrEqualTo($inicioSemana)) {
                $sesionesSemana++;
            }
        }

        // Últimos 7 días (de más antiguo a hoy)
        $semana = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i);
            $semana[] = [
                'etiqueta' => self::DIAS_SEMANA[$fecha->dayOfWeek],
                'reps'     => $repsPorDia[$fecha->format('Y-m-d')] ?? 0,
            ];
        }

        // Repeticiones por semana (lunes a domingo), últimas 8 semanas
        $semanas = [];
        for ($i = 7; $i >= 0; $i--) {
            $inicio = now()->startOfWeek()->subWeeks($i);
            $total = 0;
            for ($d = 0; $d < 7; $d++) {
                $total += $repsPorDia[$inicio->copy()->addDays($d)->format('Y-m-d')] ?? 0;
            }
            $semanas[] = [
                'etiqueta' => $inicio->format('d/m'),
                'reps'     => $total,
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

        // Evolución del puntaje de técnica: promedio por día, últimos 30 días
        ksort($puntajesPorDia);
        $evolucionPuntaje = [];
        foreach ($puntajesPorDia as $dia => $puntajes) {
            if ($dia < $desde30) {
                continue;
            }
            $evolucionPuntaje[] = [
                'etiqueta' => date('d/m', strtotime($dia)),
                'puntaje'  => (int) round(array_sum($puntajes) / count($puntajes)),
            ];
        }

        // Meta semanal
        $meta = [
            'objetivo'   => self::META_SEMANAL,
            'hechas'     => $sesionesSemana,
            'porcentaje' => min(100, (int) round($sesionesSemana / self::META_SEMANAL * 100)),
        ];

        return [
            'sesiones'         => (int) $totales->sesiones,
            'repeticiones'     => (int) $totales->repeticiones,
            'segundos'         => (int) $totales->segundos,
            'mejor'            => (int) $totales->mejor,
            'puntaje_promedio' => $totales->puntaje_promedio !== null ? (int) round($totales->puntaje_promedio) : null,
            'por_ejercicio'    => $porEjercicio,
            'semana'           => $semana,
            'max_semana'       => max(array_column($semana, 'reps')),
            'semanas'          => $semanas,
            'evolucion_puntaje' => $evolucionPuntaje,
            'meta'             => $meta,
            'racha'            => $racha,
            'dias_activos'     => $diasActivos30,
        ];
    }
}
