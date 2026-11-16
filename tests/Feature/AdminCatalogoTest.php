<?php

namespace Tests\Feature;

use App\Models\Ejercicio;
use App\Models\PhysicalProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCatalogoTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function conPerfil(): User
    {
        $user = User::factory()->create();
        PhysicalProfile::create([
            'user_id' => $user->id, 'weight' => 70, 'height' => 175, 'birth_date' => '1996-06-15',
            'gender' => 'Masculino', 'activity_level' => 'Moderado',
        ]);

        return $user;
    }

    private function sesion(User $user, string $ejercicio = 'sentadilla')
    {
        return $this->actingAs($user)->postJson('/entrenamiento/sesiones', [
            'ejercicio' => $ejercicio, 'repeticiones' => 5, 'tiempo_segundos' => 30, 'puntaje' => 80,
        ]);
    }

    public function test_solo_un_admin_ve_y_edita_el_catalogo(): void
    {
        $user = User::factory()->create();
        $e = Ejercicio::where('clave', 'sentadilla')->firstOrFail();

        $this->actingAs($user)->get('/admin/ejercicios')->assertForbidden();
        $this->actingAs($user)->put(route('admin.ejercicios.update', $e), ['nombre' => 'X'])->assertForbidden();
        $this->actingAs($user)->patch(route('admin.ejercicios.activo', $e))->assertForbidden();

        $this->actingAs($this->admin())->get('/admin/ejercicios')->assertOk()->assertSee('sentadilla');
    }

    public function test_editar_nombre_y_descripcion(): void
    {
        $e = Ejercicio::where('clave', 'biceps')->firstOrFail();

        $this->actingAs($this->admin())->put(route('admin.ejercicios.update', $e), [
            'nombre' => 'Curl de bíceps con mancuerna',
            'descripcion' => 'Codos pegados al cuerpo.',
        ])->assertSessionHasNoErrors();

        $e->refresh();
        $this->assertSame('Curl de bíceps con mancuerna', $e->nombre);
        $this->assertSame('Codos pegados al cuerpo.', $e->descripcion);
        $this->assertSame('biceps', $e->clave);
    }

    public function test_nombre_obligatorio_y_unico(): void
    {
        $admin = $this->admin();
        $a = Ejercicio::where('clave', 'sentadilla')->firstOrFail();
        $b = Ejercicio::where('clave', 'biceps')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.ejercicios.update', $a), ['nombre' => ''])
            ->assertSessionHasErrors('nombre', null, 'ejercicio_'.$a->id);

        $this->actingAs($admin)->put(route('admin.ejercicios.update', $a), ['nombre' => $b->nombre])
            ->assertSessionHasErrors('nombre', null, 'ejercicio_'.$a->id);
    }

    public function test_un_ejercicio_desactivado_sale_del_detector_y_no_acepta_sesiones(): void
    {
        $admin = $this->admin();
        $user = $this->conPerfil();
        $e = Ejercicio::where('clave', 'sentadilla')->firstOrFail();

        $this->actingAs($user)->get('/entrenamiento/detector')
            ->assertOk()
            ->assertSee('value="sentadilla"', false)
            ->assertSee('value="biceps"', false);

        $this->actingAs($admin)->patch(route('admin.ejercicios.activo', $e));
        $this->assertFalse($e->fresh()->activo);

        $this->actingAs($user)->get('/entrenamiento/detector')
            ->assertOk()
            ->assertDontSee('value="sentadilla"', false)
            ->assertSee('value="biceps"', false);

        $this->sesion($user, 'sentadilla')->assertStatus(422);
        $this->sesion($user, 'biceps')->assertCreated();

        $this->actingAs($admin)->patch(route('admin.ejercicios.activo', $e));
        $this->sesion($user, 'sentadilla')->assertCreated();
    }

    public function test_el_detector_muestra_nombre_y_descripcion_editados(): void
    {
        $user = $this->conPerfil();
        Ejercicio::where('clave', 'flexion')->update(['nombre' => 'Flexión clásica', 'descripcion' => 'Cuerpo en línea recta.']);

        $this->actingAs($user)->get('/entrenamiento/detector')
            ->assertSee('Flexión clásica')
            ->assertSee('Cuerpo en línea recta.');
    }

    public function test_no_se_puede_desactivar_el_ultimo_ejercicio_activo(): void
    {
        Ejercicio::where('clave', '!=', 'sentadilla')->update(['activo' => false]);
        $e = Ejercicio::where('clave', 'sentadilla')->firstOrFail();

        $this->actingAs($this->admin())->patch(route('admin.ejercicios.activo', $e))
            ->assertSessionHas('error');

        $this->assertTrue($e->fresh()->activo);
    }
}
