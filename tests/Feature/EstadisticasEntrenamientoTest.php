<?php

namespace Tests\Feature;

use App\Models\SesionEntrenamiento;
use App\Models\User;
use App\Services\EstadisticasEntrenamiento;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstadisticasEntrenamientoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo('2026-11-18 12:00:00'); // miércoles
    }

    private function sesion(User $user, string $fecha, int $reps = 10, ?int $puntaje = 80, string $ejercicio = 'sentadilla', int $segundos = 60): void
    {
        $momento = Carbon::parse($fecha);

        $sesion = new SesionEntrenamiento([
            'user_id' => $user->id,
            'ejercicio' => $ejercicio,
            'repeticiones' => $reps,
            'tiempo_segundos' => $segundos,
            'puntaje' => $puntaje,
        ]);
        $sesion->created_at = $momento;
        $sesion->updated_at = $momento;
        $sesion->save();
    }

    private function resumen(User $user): array
    {
        return (new EstadisticasEntrenamiento())->resumen($user);
    }

    public function test_usuario_sin_sesiones(): void
    {
        $r = $this->resumen(User::factory()->create());

        $this->assertSame(0, $r['sesiones']);
        $this->assertSame(0, $r['repeticiones']);
        $this->assertNull($r['puntaje_promedio']);
        $this->assertSame(0, $r['racha']);
        $this->assertSame(0, $r['dias_activos']);
        $this->assertSame(0, $r['max_semana']);
        $this->assertSame(0, $r['meta']['hechas']);
        $this->assertSame(0, $r['meta']['porcentaje']);
        $this->assertCount(7, $r['semana']);
        $this->assertCount(8, $r['semanas']);
        $this->assertSame([], $r['evolucion_puntaje']);
    }

    public function test_totales_y_promedio(): void
    {
        $user = User::factory()->create();
        $this->sesion($user, '2026-11-18 08:00', 10, 80, 'sentadilla', 60);
        $this->sesion($user, '2026-11-18 09:00', 20, 60, 'sentadilla', 120);

        $r = $this->resumen($user);

        $this->assertSame(2, $r['sesiones']);
        $this->assertSame(30, $r['repeticiones']);
        $this->assertSame(180, $r['segundos']);
        $this->assertSame(20, $r['mejor']);
        $this->assertSame(70, $r['puntaje_promedio']);
    }

    public function test_el_puntaje_nulo_no_entra_en_el_promedio(): void
    {
        $user = User::factory()->create();
        $this->sesion($user, '2026-11-18 08:00', 10, null);
        $this->sesion($user, '2026-11-18 09:00', 10, 90);

        $this->assertSame(90, $this->resumen($user)['puntaje_promedio']);
    }

    public function test_no_mezcla_sesiones_de_otros_usuarios(): void
    {
        $user = User::factory()->create();
        $otro = User::factory()->create();
        $this->sesion($user, '2026-11-18 08:00', 10);
        $this->sesion($otro, '2026-11-18 08:00', 99);

        $r = $this->resumen($user);

        $this->assertSame(1, $r['sesiones']);
        $this->assertSame(10, $r['repeticiones']);
    }

    public function test_agrupa_por_ejercicio_ordenado_por_repeticiones(): void
    {
        $user = User::factory()->create();
        $this->sesion($user, '2026-11-18 08:00', 5, 80, 'biceps');
        $this->sesion($user, '2026-11-18 09:00', 15, 80, 'sentadilla');
        $this->sesion($user, '2026-11-17 09:00', 10, 80, 'sentadilla');

        $r = $this->resumen($user);

        $this->assertCount(2, $r['por_ejercicio']);
        $this->assertSame('sentadilla', $r['por_ejercicio']->first()->ejercicio);
        $this->assertSame(25, (int) $r['por_ejercicio']->first()->repeticiones);
    }

    public function test_racha_de_dias_consecutivos_incluyendo_hoy(): void
    {
        $user = User::factory()->create();
        $this->sesion($user, '2026-11-18 08:00');
        $this->sesion($user, '2026-11-17 08:00');
        $this->sesion($user, '2026-11-16 08:00');
        $this->sesion($user, '2026-11-14 08:00'); // hay un hueco el 15

        $this->assertSame(3, $this->resumen($user)['racha']);
    }

    public function test_racha_cuenta_desde_ayer_si_hoy_no_entreno(): void
    {
        $user = User::factory()->create();
        $this->sesion($user, '2026-11-17 08:00');
        $this->sesion($user, '2026-11-16 08:00');

        $this->assertSame(2, $this->resumen($user)['racha']);
    }

    public function test_racha_es_cero_si_pasaron_dos_dias(): void
    {
        $user = User::factory()->create();
        $this->sesion($user, '2026-11-16 08:00');

        $this->assertSame(0, $this->resumen($user)['racha']);
    }

    public function test_dias_activos_solo_cuenta_los_ultimos_30_dias(): void
    {
        $user = User::factory()->create();
        $this->sesion($user, '2026-11-18 08:00');
        $this->sesion($user, '2026-11-13 08:00');
        $this->sesion($user, '2026-10-09 08:00'); // hace 40 días

        $this->assertSame(2, $this->resumen($user)['dias_activos']);
    }

    public function test_meta_semanal_cuenta_desde_el_lunes(): void
    {
        $user = User::factory()->create();
        $this->sesion($user, '2026-11-15 20:00'); // domingo: semana anterior
        $this->sesion($user, '2026-11-16 08:00');
        $this->sesion($user, '2026-11-17 08:00');

        $meta = $this->resumen($user)['meta'];

        $this->assertSame(3, $meta['objetivo']);
        $this->assertSame(2, $meta['hechas']);
        $this->assertSame(67, $meta['porcentaje']);
    }

    public function test_meta_semanal_no_pasa_del_100_por_ciento(): void
    {
        $user = User::factory()->create();
        foreach (['08:00', '09:00', '10:00', '11:00'] as $hora) {
            $this->sesion($user, "2026-11-18 {$hora}");
        }

        $meta = $this->resumen($user)['meta'];

        $this->assertSame(4, $meta['hechas']);
        $this->assertSame(100, $meta['porcentaje']);
    }

    public function test_evolucion_del_puntaje_promedia_por_dia_y_ordena(): void
    {
        $user = User::factory()->create();
        $this->sesion($user, '2026-11-18 08:00', 10, 80);
        $this->sesion($user, '2026-11-18 09:00', 10, 60);
        $this->sesion($user, '2026-11-17 08:00', 10, 50);

        $r = $this->resumen($user);

        $this->assertSame([
            ['etiqueta' => '17/11', 'puntaje' => 50],
            ['etiqueta' => '18/11', 'puntaje' => 70],
        ], $r['evolucion_puntaje']);
    }

    public function test_ultimos_7_dias_terminan_en_hoy(): void
    {
        $user = User::factory()->create();
        $this->sesion($user, '2026-11-18 08:00', 12);
        $this->sesion($user, '2026-11-12 08:00', 7); // hace 6 días: primer día de la serie

        $r = $this->resumen($user);

        $this->assertSame(12, $r['semana'][6]['reps']);
        $this->assertSame(7, $r['semana'][0]['reps']);
        $this->assertSame(12, $r['max_semana']);
    }
}