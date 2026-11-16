<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Administración · Ejercicios</h2>
    </x-slot>

    @php $btn = 'rounded-md px-4 py-2 text-sm font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500'; @endphp

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6 px-4">
            @include('admin._menu')

            <p class="text-sm text-gray-600 dark:text-gray-300">
                Aquí puedes cambiar el nombre y la descripción de cada ejercicio y activarlo o desactivarlo. La lógica de detección de cada uno vive en el detector, por eso no se pueden crear ejercicios nuevos desde este panel. Un ejercicio desactivado desaparece del detector y no admite sesiones nuevas; las sesiones antiguas se conservan.
            </p>

            @foreach ($ejercicios as $e)
                @php
                    $bag = $errors->getBag('ejercicio_'.$e->id);
                    $propio = (string) old('_id') === (string) $e->id;
                    $nombre = $propio ? old('nombre') : $e->nombre;
                    $descripcion = $propio ? old('descripcion') : $e->descripcion;
                @endphp
                <section class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $e->nombre }}</h3>
                            <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ $e->clave }}</code>
                            @if ($e->activo)
                                <span class="rounded bg-green-100 px-2 py-0.5 text-xs text-green-800 dark:bg-green-900/40 dark:text-green-200">Activo</span>
                            @else
                                <span class="rounded bg-red-100 px-2 py-0.5 text-xs text-red-800 dark:bg-red-900/40 dark:text-red-200">Desactivado</span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $e->sesiones_count }} sesiones registradas</span>
                    </div>

                    <form method="POST" action="{{ route('admin.ejercicios.update', $e) }}" class="mt-4 space-y-3">
                        @csrf @method('PUT')
                        <input type="hidden" name="_id" value="{{ $e->id }}">
                        <div>
                            <label for="nombre-{{ $e->id }}" class="block text-sm text-gray-700 dark:text-gray-300">Nombre</label>
                            <input id="nombre-{{ $e->id }}" type="text" name="nombre" value="{{ $nombre }}" maxlength="60" required
                                   class="mt-1 w-full max-w-md rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            @if ($bag->has('nombre')) <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $bag->first('nombre') }}</p> @endif
                        </div>
                        <div>
                            <label for="descripcion-{{ $e->id }}" class="block text-sm text-gray-700 dark:text-gray-300">Descripción (se muestra en el detector)</label>
                            <textarea id="descripcion-{{ $e->id }}" name="descripcion" rows="2" maxlength="300"
                                      class="mt-1 w-full rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ $descripcion }}</textarea>
                            @if ($bag->has('descripcion')) <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $bag->first('descripcion') }}</p> @endif
                        </div>
                        <button type="submit" class="{{ $btn }} bg-blue-600 text-white hover:bg-blue-700">Guardar</button>
                    </form>

                    <form method="POST" action="{{ route('admin.ejercicios.activo', $e) }}" class="mt-3 border-t border-gray-200 pt-3 dark:border-gray-700">
                        @csrf @method('PATCH')
                        <button type="submit" class="{{ $btn }} {{ $e->activo ? 'bg-amber-500 text-white hover:bg-amber-600' : 'bg-green-600 text-white hover:bg-green-700' }}">
                            {{ $e->activo ? 'Desactivar ejercicio' : 'Activar ejercicio' }}
                        </button>
                    </form>
                </section>
            @endforeach
        </div>
    </div>
</x-app-layout>
