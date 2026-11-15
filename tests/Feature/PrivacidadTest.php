<?php

namespace Tests\Feature;

use App\Models\ConsultaIa;
use App\Models\PhysicalProfile;
use App\Models\SesionEntrenamiento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivacidadTest extends TestCase
{
    use RefreshDatabase;

    private function datosDe(User $user): void
    {
        PhysicalProfile::create([
            'user_id' => $user->id,
            'weight' => 70,
            'height' => 175,
            'birth_date' => '1996-06-15',
            'gender' => 'Masculino',
            'activity_level' => 'Moderado',
            'medical_conditions' => 'Rodilla delicada',
        ]);

        foreach ([10, 20] as $reps) {
            SesionEntrenamiento::create([
                'user_id' => $user->id,
                'ejercicio' => 'sentadilla',
                'repeticiones' => $reps,
                'tiempo_segundos' => 60,
                'puntaje' => 80,
            ]);
        }

        ConsultaIa::create([
            'user_id' => $user->id,
            'pregunta' => '¿Cuánto descanso necesito?',
            'respuesta' => 'Entre 48 y 72 horas.',
            'modelo' => 'gemini-test',
        ]);
    }

    public function test_la_politica_es_publica(): void
    {
        $this->get('/privacidad')
            ->assertOk()
            ->assertSee('Política de privacidad y aviso médico');
    }

    public function test_la_exportacion_exige_sesion(): void
    {
        $this->get('/profile/exportar')->assertRedirect('/login');
    }

    public function test_la_exportacion_incluye_los_datos_del_usuario(): void
    {
        $user = User::factory()->create();
        $this->datosDe($user);

        $response = $this->actingAs($user)->get('/profile/exportar');

        $response
            ->assertOk()
            ->assertJsonPath('cuenta.correo', $user->email)
            ->assertJsonPath('perfil_fisico.genero', 'Masculino')
            ->assertJsonPath('perfil_fisico.condiciones_medicas', 'Rodilla delicada')
            ->assertJsonCount(2, 'sesiones_entrenamiento')
            ->assertJsonCount(1, 'consultas_asistente_ia')
            ->assertJsonPath('consultas_asistente_ia.0.modelo', 'gemini-test');

        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
    }

    public function test_la_exportacion_no_incluye_la_contrasena(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile/exportar');

        $this->assertStringNotContainsString($user->password, $response->getContent());
    }

    public function test_la_exportacion_sin_perfil_devuelve_null(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/profile/exportar')
            ->assertOk()
            ->assertJsonPath('perfil_fisico', null)
            ->assertJsonCount(0, 'sesiones_entrenamiento');
    }

    public function test_la_exportacion_no_mezcla_datos_de_otros_usuarios(): void
    {
        $user = User::factory()->create();
        $otro = User::factory()->create();
        $this->datosDe($otro);

        $this->actingAs($user)->get('/profile/exportar')
            ->assertOk()
            ->assertJsonPath('perfil_fisico', null)
            ->assertJsonCount(0, 'sesiones_entrenamiento')
            ->assertJsonCount(0, 'consultas_asistente_ia');
    }

    public function test_borrar_la_cuenta_elimina_todos_sus_datos_y_respeta_los_de_otros(): void
    {
        $user = User::factory()->create();
        $otro = User::factory()->create();
        $this->datosDe($user);
        $this->datosDe($otro);

        $this->actingAs($user)
            ->delete('/profile', ['password' => 'password'])
            ->assertRedirect('/');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('physical_profiles', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('sesion_entrenamientos', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('consultas_ia', ['user_id' => $user->id]);

        $this->assertDatabaseHas('users', ['id' => $otro->id]);
        $this->assertDatabaseHas('physical_profiles', ['user_id' => $otro->id]);
        $this->assertSame(2, SesionEntrenamiento::where('user_id', $otro->id)->count());
        $this->assertSame(1, ConsultaIa::where('user_id', $otro->id)->count());
    }
}