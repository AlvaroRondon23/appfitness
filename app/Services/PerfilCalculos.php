<?php

namespace App\Services;

use App\Models\PhysicalProfile;
use Carbon\Carbon;

/**
 * Cálculos del perfil físico (IMC, gasto energético y macronutrientes).
 * Se usa en el dashboard y en el contexto del asistente de IA, para que ambos coincidan.
 */
class PerfilCalculos
{
    // Multiplicadores habituales para estimar el gasto diario total
    private const FACTORES_ACTIVIDAD = [
        'Sedentario' => 1.2,
        'Moderado'   => 1.55,
        'Activo'     => 1.725,
    ];

    public function para(PhysicalProfile $perfil): array
    {
        $peso   = (float) $perfil->weight;
        $altura = (int) $perfil->height;
        $edad   = Carbon::parse($perfil->birth_date)->age;

        $metros = $altura / 100;
        $imc = $metros > 0 ? round($peso / ($metros * $metros), 1) : 0;

        $categoria = match (true) {
            $imc < 18.5 => 'Bajo Peso',
            $imc < 25   => 'Normal',
            $imc < 30   => 'Sobrepeso',
            default     => 'Obesidad',
        };

        // Mifflin-St Jeor: +5 hombres, -161 mujeres. Para "Otro" se usa el punto medio.
        $ajusteGenero = match ($perfil->gender) {
            'Masculino' => 5,
            'Femenino'  => -161,
            default     => -78,
        };

        $tmb  = (int) round((10 * $peso) + (6.25 * $altura) - (5 * $edad) + $ajusteGenero);
        $tdee = (int) round($tmb * (self::FACTORES_ACTIVIDAD[$perfil->activity_level] ?? 1.2));

        return [
            'edad'          => $edad,
            'imc'           => $imc,
            'categoria_imc' => $categoria,
            'tmb'           => $tmb,
            'tdee'          => $tdee,
            // Reparto base: 40% carbohidratos, 30% proteínas, 30% grasas sobre el gasto diario
            'carbs'         => (int) round(($tdee * 0.40) / 4),
            'proteinas'     => (int) round(($tdee * 0.30) / 4),
            'grasas'        => (int) round(($tdee * 0.30) / 9),
        ];
    }
}
