<?php

namespace Tests\Feature;

use App\Models\PhysicalProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SesionEntrenamientoTest extends TestCase
{
    use RefreshDatabase;

    private function usuarioConPerfil(): User
    {
        $user = User::factory()->create();

        PhysicalProfile::create([
            'user_id' => $user->id,
            'weight' => 70,
            'height' => 175,
            'birth_date' => '1996-06-15',
            'gender' => 'Masculino',
            'activity_level' => 'Moderado',
        ]);

        return $user;
    }

    private function datos(array $cambios = []): array
    {
        return array_merge([
            'ejercicio' => 'sentadilla',
            'repeticiones' => 10,
            'tiempo_segundos' => 45,
            'puntaje' => 85,
        ], $cambios);
    }

    public static function ejerciciosDelCatalogo(): array
    {
        return [
            'sentadilla' => ['sentadilla'],
            'biceps' => ['biceps'],
            'flexion' => ['flexion'],
            'salto de tijera' => ['salto_tijera'],
            'elevacion de rodillas' => ['elevacion_rodillas'],
        ];
    }

    public function test_el_catalogo_incluye_los_ejercicios_nuevos(): void
    {
        $this->assertDatabaseHas('ejercicios', ['clave' => 'salto_tijera', 'nombre' => 'Salto de tijera']);
        $this->assertDatabaseHas('ejercicios', ['clave' => 'elevacion_rodillas', 'nombre' => 'Elevación de rodillas']);
    }

    #[DataProvider('ejerciciosDelCatalogo')]
    public function test_guarda_la_sesion_de_cada_ejercicio_del_catalogo(string $clave): void
    {
        $user = $this->usuarioConPerfil();

        $this->actingAs($user)
            ->postJson(route('entrenamiento.sesiones.store'), $this->datos(['ejercicio' => $clave]))
            ->assertCreated()
            ->assertJsonPath('ok', true);

        $this->assertDatabaseHas('sesion_entrenamientos', [
            'user_id' => $user->id,
            'ejercicio' => $clave,
            'repeticiones' => 10,
            'puntaje' => 85,
        ]);
    }

    public function test_rechaza_un_ejercicio_desconocido(): void
    {
        $user = $this->usuarioConPerfil();

        $this->actingAs($user)
            ->postJson(route('entrenamiento.sesiones.store'), $this->datos(['ejercicio' => 'burpee']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('ejercicio');

        $this->assertDatabaseCount('sesion_entrenamientos', 0);
    }

    public function test_rechaza_repeticiones_fuera_de_rango(): void
    {
        $user = $this->usuarioConPerfil();

        $this->actingAs($user)
            ->postJson(route('entrenamiento.sesiones.store'), $this->datos(['ejercicio' => 'salto_tijera', 'repeticiones' => 0]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('repeticiones');
    }

    public function test_el_puntaje_es_opcional_pero_no_puede_pasar_de_100(): void
    {
        $user = $this->usuarioConPerfil();

        $this->actingAs($user)
            ->postJson(route('entrenamiento.sesiones.store'), $this->datos(['ejercicio' => 'elevacion_rodillas', 'puntaje' => null]))
            ->assertCreated();

        $this->actingAs($user)
            ->postJson(route('entrenamiento.sesiones.store'), $this->datos(['ejercicio' => 'elevacion_rodillas', 'puntaje' => 101]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('puntaje');
    }

    public function test_exige_sesion_iniciada(): void
    {
        $this->postJson(route('entrenamiento.sesiones.store'), $this->datos(['ejercicio' => 'salto_tijera']))
            ->assertUnauthorized();

        $this->assertDatabaseCount('sesion_entrenamientos', 0);
    }

    public function test_el_usuario_se_toma_de_la_sesion_y_no_del_navegador(): void
    {
        $user = $this->usuarioConPerfil();
        $otro = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('entrenamiento.sesiones.store'), $this->datos(['ejercicio' => 'salto_tijera', 'user_id' => $otro->id]))
            ->assertCreated();

        $this->assertDatabaseHas('sesion_entrenamientos', ['user_id' => $user->id, 'ejercicio' => 'salto_tijera']);
        $this->assertDatabaseMissing('sesion_entrenamientos', ['user_id' => $otro->id]);
    }
}
