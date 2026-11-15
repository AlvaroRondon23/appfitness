<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Descarga en JSON todos los datos que la app guarda del usuario.
     */
    public function exportar(Request $request): JsonResponse
    {
        $user = $request->user();
        $perfil = $user->physicalProfile;

        $datos = [
            'exportado_el' => now()->toIso8601String(),
            'cuenta' => [
                'nombre' => $user->name,
                'correo' => $user->email,
                'creada_el' => $user->created_at?->toIso8601String(),
                'politica_aceptada_el' => $user->privacy_accepted_at?->toIso8601String(),
            ],
            'perfil_fisico' => $perfil ? [
                'peso_kg' => $perfil->weight,
                'estatura_cm' => $perfil->height,
                'fecha_nacimiento' => (string) $perfil->birth_date,
                'genero' => $perfil->gender,
                'nivel_actividad' => $perfil->activity_level,
                'condiciones_medicas' => $perfil->medical_conditions,
            ] : null,
            'sesiones_entrenamiento' => $user->sesiones()->orderBy('id')->get()->map(fn ($s) => [
                'fecha' => $s->created_at?->toIso8601String(),
                'ejercicio' => $s->ejercicio,
                'repeticiones' => $s->repeticiones,
                'tiempo_segundos' => $s->tiempo_segundos,
                'puntaje_tecnica' => $s->puntaje,
            ])->values(),
            'consultas_asistente_ia' => $user->consultasIa()->orderBy('id')->get()->map(fn ($c) => [
                'fecha' => $c->created_at?->toIso8601String(),
                'pregunta' => $c->pregunta,
                'respuesta' => $c->respuesta,
                'modelo' => $c->modelo,
            ])->values(),
        ];

        return response()->json(
            $datos,
            200,
            ['Content-Disposition' => 'attachment; filename="appfitness-mis-datos-'.now()->format('Y-m-d').'.json"'],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        DB::transaction(function () use ($user) {
            // Cierra también las sesiones abiertas en otros dispositivos (tabla sessions, sin clave foránea)
            if (config('session.driver') === 'database') {
                DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
            }

            // El perfil físico, las sesiones de entrenamiento y las consultas a la IA
            // se eliminan en cascada (claves foráneas con cascadeOnDelete)
            $user->delete();
        });

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
