<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BorradoCuenta;
use App\Services\EstadisticasEntrenamiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $usuarios = User::query()
            ->withCount('sesiones')
            ->withMax('sesiones as ultima_sesion', 'created_at')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.addcslashes($q, '%_\\').'%';
                $query->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('email', 'like', $like));
            })
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.usuarios.index', compact('usuarios', 'q'));
    }

    /**
     * Ficha de un usuario: cuenta y resumen de entrenamiento.
     * No muestra el perfil físico (peso, estatura, condiciones médicas) ni las consultas a la IA.
     */
    public function show(User $user, EstadisticasEntrenamiento $estadisticas): View
    {
        return view('admin.usuarios.show', [
            'usuario'     => $user,
            'stats'       => $estadisticas->resumen($user),
            'sesiones'    => $user->sesiones()->orderByDesc('id')->take(10)->get(),
            'tienePerfil' => $user->physicalProfile()->exists(),
        ]);
    }

    public function estado(Request $request, User $user): RedirectResponse
    {
        if ($motivo = $this->motivoBloqueo($request, $user)) {
            return back()->with('error', $motivo);
        }

        $desactivar = ! $user->estaDesactivada();
        $user->forceFill(['disabled_at' => $desactivar ? now() : null])->save();

        return back()->with('status', $desactivar ? 'Cuenta desactivada.' : 'Cuenta reactivada.');
    }

    public function rol(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'No puedes cambiar tu propio rol.');
        }

        $datos = $request->validate([
            'role' => ['required', Rule::in(User::ROLES)],
        ]);

        $user->forceFill(['role' => $datos['role']])->save();

        return back()->with('status', 'Rol actualizado.');
    }

    public function destroy(Request $request, User $user, BorradoCuenta $borrado): RedirectResponse
    {
        if ($motivo = $this->motivoBloqueo($request, $user)) {
            return back()->with('error', $motivo);
        }

        $request->validateWithBag('eliminarUsuario', [
            'password' => ['required', 'current_password'],
        ]);

        $borrado->eliminar($user);

        return redirect()->route('admin.usuarios.index')->with('status', 'Cuenta eliminada con todos sus datos.');
    }

    /** Impide actuar sobre la propia cuenta o sobre otro administrador (hay que quitarle el rol antes). */
    private function motivoBloqueo(Request $request, User $user): ?string
    {
        if ($user->is($request->user())) {
            return 'No puedes hacer esto con tu propia cuenta.';
        }

        if ($user->esAdmin()) {
            return 'Es una cuenta de administrador. Quítale primero el rol de administrador.';
        }

        return null;
    }
}
