<?php

namespace Tests\Feature;

use App\Models\ConsultaIa;
use App\Models\PhysicalProfile;
use App\Models\RegistroIa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminAsistenteTest extends TestCase
{
    use RefreshDatabase;

    private function conPerfil(): User
    {
        $user = User::factory()->create();
        PhysicalProfile::create([
            'user_id' => $user->id, 'weight' => 70, 'height' => 175, 'birth_date' => '1996-06-15',
            'gender' => 'Masculino', 'activity_level' => 'Moderado',
        ]);

        return $user;
    }

    private function registro(User $user, string $resultado, array $extra = []): RegistroIa
    {
        return RegistroIa::create(array_merge([
            'user_id' => $user->id, 'resultado' => $resultado, 'modelo' => 'm1',
            'duracion_ms' => 1500, 'caracteres_pregunta' => 20, 'caracteres_respuesta' => 100,
        ], $extra));
    }

    private function consultar(User $user)
    {
        return $this->actingAs($user)->postJson('/asistente/consultar', ['prompt' => 'secreto-pregunta']);
    }

    // ---------- Panel ----------

    public function test_solo_un_admin_ve_la_supervision(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/asistente')->assertForbidden();

        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/admin/asistente')->assertOk();
    }

    public function test_el_panel_resume_el_uso_y_no_muestra_el_contenido(): void
    {
        $user = User::factory()->create(['name' => 'Persona Uno']);
        $this->registro($user, 'ok');
        $this->registro($user, 'ok');
        $this->registro($user, 'bloqueada', ['modelo' => null, 'caracteres_respuesta' => null]);
        ConsultaIa::create(['user_id' => $user->id, 'pregunta' => 'pregunta-confidencial', 'respuesta' => 'respuesta-confidencial', 'modelo' => 'm1']);

        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/admin/asistente')
            ->assertOk()
            ->assertViewHas('total', 3)
            ->assertViewHas('exito', 67)
            ->assertSee('Persona Uno')
            ->assertDontSee('pregunta-confidencial')
            ->assertDontSee('respuesta-confidencial');
    }

    public function test_el_filtro_por_resultado(): void
    {
        $user = User::factory()->create(['name' => 'Persona Uno']);
        $this->registro($user, 'ok');
        $this->registro($user, 'sobrecarga', ['modelo' => 'modelo-saturado']);

        $this->actingAs(User::factory()->create(['role' => 'admin']))->get('/admin/asistente?resultado=sobrecarga')
            ->assertOk()
            ->assertSee('modelo-saturado');
    }

    // ---------- El asistente deja su registro técnico ----------

    public function test_una_consulta_correcta_deja_registro_sin_contenido(): void
    {
        config(['services.gemini.key' => 'clave-falsa', 'services.gemini.models' => ['m1']]);
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => 'Hola']]]]],
        ], 200)]);

        $user = $this->conPerfil();
        $this->consultar($user)->assertOk();

        $r = RegistroIa::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('ok', $r->resultado);
        $this->assertSame('m1', $r->modelo);
        $this->assertSame(4, $r->caracteres_respuesta);
        $this->assertSame(mb_strlen('secreto-pregunta'), $r->caracteres_pregunta);
        $this->assertStringNotContainsString('secreto', json_encode($r->toArray()));
    }

    public function test_un_servicio_saturado_deja_registro(): void
    {
        config(['services.gemini.key' => 'clave-falsa', 'services.gemini.models' => ['m1']]);
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response([], 503)]);

        $user = $this->conPerfil();
        $this->consultar($user)->assertStatus(503);

        $r = RegistroIa::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('sobrecarga', $r->resultado);
        $this->assertSame(503, $r->http_status);
    }

    public function test_sin_clave_deja_registro(): void
    {
        config(['services.gemini.key' => null]);

        $user = $this->conPerfil();
        $this->consultar($user)->assertStatus(500);

        $this->assertSame('sin_clave', RegistroIa::where('user_id', $user->id)->value('resultado'));
    }

    public function test_limpiar_el_historial_conserva_el_registro_tecnico(): void
    {
        $user = $this->conPerfil();
        $this->registro($user, 'ok');
        ConsultaIa::create(['user_id' => $user->id, 'pregunta' => 'p', 'respuesta' => 'r', 'modelo' => 'm1']);

        $this->actingAs($user)->delete('/asistente/historial');

        $this->assertSame(0, ConsultaIa::where('user_id', $user->id)->count());
        $this->assertSame(1, RegistroIa::where('user_id', $user->id)->count());
    }

    // ---------- Privacidad ----------

    public function test_borrar_la_cuenta_elimina_su_registro_tecnico(): void
    {
        $user = $this->conPerfil();
        $otro = User::factory()->create();
        $this->registro($user, 'ok');
        $this->registro($otro, 'ok');

        $this->actingAs($user)->delete('/profile', ['password' => 'password'])->assertRedirect('/');

        $this->assertSame(0, RegistroIa::where('user_id', $user->id)->count());
        $this->assertSame(1, RegistroIa::where('user_id', $otro->id)->count());
    }

    public function test_la_exportacion_incluye_el_registro_tecnico(): void
    {
        $user = User::factory()->create();
        $this->registro($user, 'ok');

        $this->actingAs($user)->get('/profile/exportar')
            ->assertOk()
            ->assertJsonCount(1, 'registro_tecnico_asistente')
            ->assertJsonPath('registro_tecnico_asistente.0.resultado', 'ok');
    }
}
