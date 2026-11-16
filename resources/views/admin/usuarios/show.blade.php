<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Administración · {{ $usuario->name }}</h2>
    </x-slot>

    @php
        $esYo = $usuario->is(auth()->user());
        $btn = 'rounded-md px-4 py-2 text-sm font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500';
    @endphp

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6 px-4">
            @include('admin._menu')

            <p><a href="{{ route('admin.usuarios.index') }}" class="text-sm text-blue-600 underline dark:text-blue-400">&larr; Volver al listado</a></p>

            <section class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                <h3 class="font-semibold text-gray-900 dark:text-white">Cuenta</h3>
                <dl class="mt-3 grid gap-3 text-sm sm:grid-cols-2 text-gray-700 dark:text-gray-300">
                    <div><dt class="text-xs text-gray-500 dark:text-gray-400">Nombre</dt><dd>{{ $usuario->name }}</dd></div>
                    <div><dt class="text-xs text-gray-500 dark:text-gray-400">Correo</dt><dd>{{ $usuario->email }}</dd></div>
                    <div><dt class="text-xs text-gray-500 dark:text-gray-400">Rol</dt><dd>{{ $usuario->esAdmin() ? 'Administrador' : 'Usuario' }}</dd></div>
                    <div><dt class="text-xs text-gray-500 dark:text-gray-400">Estado</dt><dd>{{ $usuario->estaDesactivada() ? 'Desactivada el '.$usuario->disabled_at->format('d/m/Y') : 'Activa' }}</dd></div>
                    <div><dt class="text-xs text-gray-500 dark:text-gray-400">Registro</dt><dd>{{ $usuario->created_at?->format('d/m/Y H:i') }}</dd></div>
                    <div><dt class="text-xs text-gray-500 dark:text-gray-400">Política aceptada</dt><dd>{{ $usuario->privacy_accepted_at?->format('d/m/Y') ?? 'No' }}</dd></div>
                    <div><dt class="text-xs text-gray-500 dark:text-gray-400">Perfil físico completado</dt><dd>{{ $tienePerfil ? 'Sí' : 'No' }}</dd></div>
                </dl>
                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Por privacidad no se muestran los datos del perfil físico ni las consultas al asistente.</p>
            </section>

            <section class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                <h3 class="font-semibold text-gray-900 dark:text-white">Entrenamiento</h3>
                <dl class="mt-3 grid grid-cols-2 gap-3 text-sm sm:grid-cols-4 text-gray-700 dark:text-gray-300">
                    <div><dt class="text-xs text-gray-500 dark:text-gray-400">Sesiones</dt><dd class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['sesiones'] }}</dd></div>
                    <div><dt class="text-xs text-gray-500 dark:text-gray-400">Repeticiones</dt><dd class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['repeticiones'] }}</dd></div>
                    <div><dt class="text-xs text-gray-500 dark:text-gray-400">Técnica promedio</dt><dd class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['puntaje_promedio'] ?? '--' }}</dd></div>
                    <div><dt class="text-xs text-gray-500 dark:text-gray-400">Racha (días)</dt><dd class="text-xl font-bold text-gray-900 dark:text-white">{{ $stats['racha'] }}</dd></div>
                </dl>

                @if ($sesiones->isNotEmpty())
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full text-sm text-gray-700 dark:text-gray-300">
                            <thead class="text-xs uppercase text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="py-2 pe-4 text-start">Fecha</th>
                                    <th class="py-2 pe-4 text-start">Ejercicio</th>
                                    <th class="py-2 pe-4 text-end">Reps</th>
                                    <th class="py-2 pe-4 text-end">Segundos</th>
                                    <th class="py-2 text-end">Técnica</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($sesiones as $s)
                                    <tr>
                                        <td class="py-2 pe-4">{{ $s->created_at?->format('d/m/Y H:i') }}</td>
                                        <td class="py-2 pe-4">{{ \Illuminate\Support\Str::headline($s->ejercicio) }}</td>
                                        <td class="py-2 pe-4 text-end">{{ $s->repeticiones }}</td>
                                        <td class="py-2 pe-4 text-end">{{ $s->tiempo_segundos }}</td>
                                        <td class="py-2 text-end">{{ $s->puntaje ?? '--' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Este usuario aún no tiene sesiones.</p>
                @endif
            </section>

            <section class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                <h3 class="font-semibold text-gray-900 dark:text-white">Acciones</h3>

                @if ($esYo)
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Esta es tu cuenta: no puedes cambiar tu rol ni desactivarla o eliminarla desde aquí.</p>
                @else
                    <div class="mt-4 space-y-5">
                        <form method="POST" action="{{ route('admin.usuarios.rol', $usuario) }}" class="flex flex-wrap items-center gap-2">
                            @csrf @method('PATCH')
                            <label for="role" class="text-sm text-gray-700 dark:text-gray-300">Rol</label>
                            <select id="role" name="role" class="rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                <option value="user" @selected(! $usuario->esAdmin())>Usuario</option>
                                <option value="admin" @selected($usuario->esAdmin())>Administrador</option>
                            </select>
                            <button type="submit" class="{{ $btn }} bg-blue-600 text-white hover:bg-blue-700">Guardar rol</button>
                            @error('role') <p class="w-full text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </form>

                        @if ($usuario->esAdmin())
                            <p class="text-sm text-gray-500 dark:text-gray-400">Para desactivar o eliminar esta cuenta, quítale antes el rol de administrador.</p>
                        @else
                            <form method="POST" action="{{ route('admin.usuarios.estado', $usuario) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="{{ $btn }} {{ $usuario->estaDesactivada() ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-amber-500 text-white hover:bg-amber-600' }}">
                                    {{ $usuario->estaDesactivada() ? 'Reactivar cuenta' : 'Desactivar cuenta' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.usuarios.destroy', $usuario) }}" class="space-y-2 border-t border-gray-200 pt-4 dark:border-gray-700"
                                  onsubmit="return confirm('¿Eliminar esta cuenta y todos sus datos? No se puede deshacer.')">
                                @csrf @method('DELETE')
                                <label for="password" class="block text-sm text-gray-700 dark:text-gray-300">Para eliminar la cuenta, confirma con tu contraseña de administrador</label>
                                <div class="flex flex-wrap gap-2">
                                    <input id="password" type="password" name="password" autocomplete="current-password"
                                           class="rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                    <button type="submit" class="{{ $btn }} bg-red-600 text-white hover:bg-red-700">Eliminar cuenta</button>
                                </div>
                                @error('password', 'eliminarUsuario') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </form>
                        @endif
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
