<?php

namespace Tests\Feature;

use App\Models\SesionEntrenamiento;
use App\Models\User;
use App\Services\EstadisticasGlobales;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EstadisticasGlobalesTest extends TestCase
{
    use RefreshDatabase;

    private function sesion(User $user, string $ejercicio, int $reps, int $puntaje, Carbon $fecha): void
    {
        $s = new SesionEntrenamiento([
            'user_id' => $user->id,
            'ejercicio' => $ejercicio,
            'repeticiones' => $reps,
            'tiempo_segundos' => 60,
            'puntaje' => $puntaje,
        ]);
        $s->created_at = $fecha;
        $s->updated_at = $fecha;
        $s->save();
    }

    public function test_resumen_global(): void
    {
        // Miércoles: la semana actual empieza el lunes 16/11/2026
        $this->travelTo(Carbon::parse('2026-11-18 12:00:00'));

        $a = User::factory()->create(['role' => 'admin']);
        $b = User::factory()->create();
        User::factory()->create()->forceFill(['disabled_at' => now()])->save();

        $this->sesion($b, 'sentadilla', 10, 80, now());                     // esta semana
        $this->sesion($b, 'sentadilla', 20, 90, now()->subDays(8));         // semana anterior
        $this->sesion($a, 'biceps', 5, 70, now()->subDays(1));              // esta semana

        $g = app(EstadisticasGlobales::class)->resumen();

        $this->assertSame(3, $g['usuarios']);
        $this->assertSame(1, $g['administradores']);
        $this->assertSame(1, $g['desactivados']);
        $this->assertSame(3, $g['nuevos_30']);
        $this->assertSame(1, $g['sin_sesiones']);
        $this->assertSame(2, $g['activos_semana']);
        $this->assertSame(2, $g['activos_30']);
        $this->assertSame(3, $g['sesiones']);
        $this->assertSame(35, $g['repeticiones']);
        $this->assertSame(80, $g['puntaje_promedio']);
        $this->assertSame('sentadilla', $g['mas_usado']);
        $this->assertCount(8, $g['semanas']);
        $this->assertSame(2, $g['semanas'][7]['sesiones']); // semana actual
        $this->assertSame(1, $g['semanas'][6]['sesiones']); // semana anterior
    }

    public function test_resumen_global_sin_datos(): void
    {
        $g = app(EstadisticasGlobales::class)->resumen();

        $this->assertSame(0, $g['sesiones']);
        $this->assertNull($g['puntaje_promedio']);
        $this->assertNull($g['mas_usado']);
        $this->assertSame(0, $g['max_semana']);
    }
}
