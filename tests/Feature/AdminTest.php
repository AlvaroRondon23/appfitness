<?php

namespace Tests\Feature;

use App\Models\PhysicalProfile;
use App\Models\SesionEntrenamiento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function sesionDe(User $user): void
    {
        SesionEntrenamiento::create([
            'user_id' => $user->id,
            'ejercicio' => 'sentadilla',
            'repeticiones' => 12,
            'tiempo_segundos' => 60,
            'puntaje' => 85,
        ]);
    }

    // ---------- Acceso ----------

    public function test_un_invitado_va_al_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/admin/usuarios')->assertRedirect('/login');
    }

    public function test_un_usuario_normal_recibe_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($user)->get('/admin/usuarios')->assertForbidden();
        $this->actingAs($user)->get(route('admin.usuarios.show', $user))->assertForbidden();
    }

    public function test_un_usuario_normal_no_puede_usar_las_acciones_de_admin(): void
    {
        $user = User::factory()->create();
        $otro = User::factory()->create();

        $this->actingAs($user)->patch(route('admin.usuarios.rol', $otro), ['role' => 'admin'])->assertForbidden();
        $this->actingAs($user)->patch(route('admin.usuarios.estado', $otro))->assertForbidden();
        $this->actingAs($user)->delete(route('admin.usuarios.destroy', $otro), ['password' => 'password'])->assertForbidden();

        $otro->refresh();
        $this->assertSame('user', $otro->role);
        $this->assertNull($otro->disabled_at);
        $this->assertDatabaseHas('users', ['id' => $otro->id]);
    }

    public function test_el_admin_ve_el_panel(): void
    {
        $this->actingAs($this->admin())->get('/admin')
            ->assertOk()
            ->assertSee('Administración');
    }

    public function test_registrarse_nunca_crea_un_admin(): void
    {
        $this->post('/register', [
            'name' => 'Nuevo',
            'email' => 'nuevo@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'privacidad' => '1',
            'role' => 'admin',
        ]);

        $this->assertSame('user', User::where('email', 'nuevo@example.com')->value('role'));
    }

    public function test_un_admin_sin_perfil_fisico_va_a_su_panel_y_un_usuario_al_formulario(): void
    {
        $this->actingAs($this->admin())->get('/dashboard')->assertRedirect(route('admin.panel'));

        $this->actingAs(User::factory()->create())->get('/dashboard')->assertRedirect(route('perfil.crear'));
    }

    // ---------- Usuarios ----------

    public function test_el_listado_filtra_por_nombre_o_correo(): void
    {
        User::factory()->create(['name' => 'Ana Prueba', 'email' => 'ana@example.com']);
        User::factory()->create(['name' => 'Luis Otro', 'email' => 'luis@example.com']);

        $this->actingAs($this->admin())->get('/admin/usuarios?q=Ana')
            ->assertOk()
            ->assertSee('Ana Prueba')
            ->assertDontSee('Luis Otro');

        $this->actingAs($this->admin())->get('/admin/usuarios?q=luis@')
            ->assertSee('Luis Otro')
            ->assertDontSee('Ana Prueba');
    }

    public function test_la_ficha_no_muestra_datos_de_salud(): void
    {
        $user = User::factory()->create();
        PhysicalProfile::create([
            'user_id' => $user->id,
            'weight' => 70,
            'height' => 175,
            'birth_date' => '1996-06-15',
            'gender' => 'Masculino',
            'activity_level' => 'Moderado',
            'medical_conditions' => 'Rodilla delicada',
        ]);
        $this->sesionDe($user);

        $this->actingAs($this->admin())->get(route('admin.usuarios.show', $user))
            ->assertOk()
            ->assertSee($user->email)
            ->assertSee('Sentadilla')
            ->assertDontSee('Rodilla delicada');
    }

    // ---------- Desactivar / reactivar ----------

    public function test_desactivar_expulsa_al_usuario_y_se_puede_reactivar(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create();

        $this->actingAs($admin)->patch(route('admin.usuarios.estado', $user))->assertRedirect();
        $this->assertNotNull($user->fresh()->disabled_at);

        $this->actingAs($user->fresh())->get('/profile')->assertRedirect('/login');
        $this->actingAs($user->fresh())->getJson('/profile')->assertForbidden();

        $this->actingAs($admin)->patch(route('admin.usuarios.estado', $user));
        $this->assertNull($user->fresh()->disabled_at);
        $this->actingAs($user->fresh())->get('/profile')->assertOk();
    }

    public function test_el_admin_no_puede_desactivarse_ni_desactivar_a_otro_admin(): void
    {
        $admin = $this->admin();
        $otroAdmin = $this->admin();

        $this->actingAs($admin)->patch(route('admin.usuarios.estado', $admin))->assertSessionHas('error');
        $this->actingAs($admin)->patch(route('admin.usuarios.estado', $otroAdmin))->assertSessionHas('error');

        $this->assertNull($admin->fresh()->disabled_at);
        $this->assertNull($otroAdmin->fresh()->disabled_at);
    }

    // ---------- Roles ----------

    public function test_el_admin_puede_promover_y_degradar_a_otro_usuario(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create();

        $this->actingAs($admin)->patch(route('admin.usuarios.rol', $user), ['role' => 'admin']);
        $this->assertTrue($user->fresh()->esAdmin());

        $this->actingAs($admin)->patch(route('admin.usuarios.rol', $user), ['role' => 'user']);
        $this->assertFalse($user->fresh()->esAdmin());
    }

    public function test_el_admin_no_puede_quitarse_su_propio_rol(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->patch(route('admin.usuarios.rol', $admin), ['role' => 'user'])
            ->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->esAdmin());
    }

    public function test_un_rol_invalido_se_rechaza(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin())->patch(route('admin.usuarios.rol', $user), ['role' => 'superadmin'])
            ->assertSessionHasErrors('role');

        $this->assertSame('user', $user->fresh()->role);
    }

    // ---------- Eliminar ----------

    public function test_el_admin_elimina_una_cuenta_con_todos_sus_datos(): void
    {
        $user = User::factory()->create();
        $this->sesionDe($user);

        $this->actingAs($this->admin())
            ->delete(route('admin.usuarios.destroy', $user), ['password' => 'password'])
            ->assertRedirect(route('admin.usuarios.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('sesion_entrenamientos', ['user_id' => $user->id]);
    }

    public function test_eliminar_exige_la_contrasena_del_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.usuarios.destroy', $user), ['password' => 'incorrecta'])
            ->assertSessionHasErrors('password', null, 'eliminarUsuario');

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_el_admin_no_puede_eliminarse_ni_eliminar_a_otro_admin_desde_el_panel(): void
    {
        $admin = $this->admin();
        $otroAdmin = $this->admin();

        $this->actingAs($admin)->delete(route('admin.usuarios.destroy', $admin), ['password' => 'password'])
            ->assertSessionHas('error');
        $this->actingAs($admin)->delete(route('admin.usuarios.destroy', $otroAdmin), ['password' => 'password'])
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
        $this->assertDatabaseHas('users', ['id' => $otroAdmin->id]);
    }

    public function test_el_unico_admin_no_puede_borrar_su_cuenta_desde_mi_cuenta(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->delete('/profile', ['password' => 'password'])
            ->assertSessionHasErrors('password', null, 'userDeletion');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_un_admin_puede_borrar_su_cuenta_si_hay_otro_admin(): void
    {
        $admin = $this->admin();
        $this->admin();

        $this->actingAs($admin)->delete('/profile', ['password' => 'password'])->assertRedirect('/');

        $this->assertDatabaseMissing('users', ['id' => $admin->id]);
    }

    public function test_la_politica_menciona_la_administracion(): void
    {
        $this->get('/privacidad')->assertOk()->assertSee('id="administracion"', false);
    }
}
