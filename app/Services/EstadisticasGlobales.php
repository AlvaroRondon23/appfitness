<?php

namespace App\Services;

use App\Models\SesionEntrenamiento;
use App\Models\User;

/**
 * Métricas agregadas de toda la aplicación (panel de administración).
 * No incluye datos de salud ni contenido de las consultas al asistente.
 */
class EstadisticasGlobales
{
    public function resumen(): array
    {
        $inicioSemana = now()->startOfWeek(); // lunes
        $desde30 = now()->subDays(29)->startOfDay();

        $totales = SesionEntrenamiento::selectRaw('COUNT(*) AS sesiones, COALESCE(SUM(repeticiones), 0) AS repeticiones, AVG(puntaje) AS puntaje_promedio')
            ->first();

        $porEjercicio = SesionEntrenamiento::selectRaw('ejercicio, COUNT(*) AS sesiones, COALESCE(SUM(repeticiones), 0) AS repeticiones, ROUND(AVG(puntaje)) AS puntaje')
            ->groupBy('ejercicio')
            ->orderByDesc('sesiones')
            ->orderBy('ejercicio')
            ->get();

        // Sesiones por semana (lunes a domingo), últimas 8 semanas; se agrupa en PHP por la zona horaria
        $desde8 = $inicioSemana->copy()->subWeeks(7);
        $conteo = [];
        foreach (SesionEntrenamiento::where('created_at', '>=', $desde8)->get(['created_at']) as $sesion) {
            $clave = $sesion->created_at->copy()->startOfWeek()->format('Y-m-d');
            $conteo[$clave] = ($conteo[$clave] ?? 0) + 1;
        }

        $semanas = [];
        for ($i = 7; $i >= 0; $i--) {
            $inicio = $inicioSemana->copy()->subWeeks($i);
            $semanas[] = [
                'etiqueta' => $inicio->format('d/m'),
                'sesiones' => $conteo[$inicio->format('Y-m-d')] ?? 0,
            ];
        }

        return [
            'usuarios'         => User::count(),
            'administradores'  => User::where('role', User::ROL_ADMIN)->count(),
            'desactivados'     => User::whereNotNull('disabled_at')->count(),
            'nuevos_30'        => User::where('created_at', '>=', $desde30)->count(),
            'sin_sesiones'     => User::doesntHave('sesiones')->count(),
            'activos_semana'   => SesionEntrenamiento::where('created_at', '>=', $inicioSemana)->distinct()->count('user_id'),
            'activos_30'       => SesionEntrenamiento::where('created_at', '>=', $desde30)->distinct()->count('user_id'),
            'sesiones'         => (int) $totales->sesiones,
            'repeticiones'     => (int) $totales->repeticiones,
            'puntaje_promedio' => $totales->puntaje_promedio !== null ? (int) round($totales->puntaje_promedio) : null,
            'por_ejercicio'    => $porEjercicio,
            'mas_usado'        => $porEjercicio->first()?->ejercicio,
            'semanas'          => $semanas,
            'max_semana'       => max(array_column($semanas, 'sesiones')),
        ];
    }
}
