<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Administración · Usuarios</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 px-4">
            @include('admin._menu')

            <form method="GET" action="{{ route('admin.usuarios.index') }}" class="flex flex-wrap gap-2">
                <label for="q" class="sr-only">Buscar por nombre o correo</label>
                <input id="q" type="search" name="q" value="{{ $q }}" placeholder="Buscar por nombre o correo"
                       class="w-full max-w-sm rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:placeholder-gray-500">
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">Buscar</button>
                @if ($q !== '')
                    <a href="{{ route('admin.usuarios.index') }}" class="px-3 py-2 text-sm text-gray-600 underline dark:text-gray-300">Limpiar</a>
                @endif
            </form>

            <div class="overflow-x-auto rounded-lg bg-white shadow-sm dark:bg-gray-800">
                <table class="min-w-full text-sm">
                    <thead class="text-start text-xs uppercase text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3 text-start">Usuario</th>
                            <th class="px-4 py-3 text-start">Rol</th>
                            <th class="px-4 py-3 text-start">Estado</th>
                            <th class="px-4 py-3 text-end">Sesiones</th>
                            <th class="px-4 py-3 text-start">Última sesión</th>
                            <th class="px-4 py-3 text-start">Registro</th>
                            <th class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 dark:divide-gray-700 dark:text-gray-300">
                        @forelse ($usuarios as $u)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $u->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $u->email }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $u->esAdmin() ? 'Administrador' : 'Usuario' }}</td>
                                <td class="px-4 py-3">
                                    @if ($u->estaDesactivada())
                                        <span class="rounded bg-red-100 px-2 py-0.5 text-xs text-red-800 dark:bg-red-900/40 dark:text-red-200">Desactivada</span>
                                    @else
                                        <span class="rounded bg-green-100 px-2 py-0.5 text-xs text-green-800 dark:bg-green-900/40 dark:text-green-200">Activa</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">{{ $u->sesiones_count }}</td>
                                <td class="px-4 py-3">{{ $u->ultima_sesion ? \Illuminate\Support\Carbon::parse($u->ultima_sesion)->format('d/m/Y') : '—' }}</td>
                                <td class="px-4 py-3">{{ $u->created_at?->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('admin.usuarios.show', $u) }}" class="text-blue-600 underline dark:text-blue-400">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No se encontraron usuarios.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $usuarios->links() }}
        </div>
    </div>
</x-app-layout>
