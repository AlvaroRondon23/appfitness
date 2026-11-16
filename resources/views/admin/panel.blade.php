<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Administración</h2>
    </x-slot>

    @php
        $tarjetas = [
            ['Usuarios', $g['usuarios'], $g['administradores'].' admin · '.$g['desactivados'].' desactivados'],
            ['Nuevos (30 días)', $g['nuevos_30'], $g['sin_sesiones'].' aún sin entrenar'],
            ['Activos esta semana', $g['activos_semana'], $g['activos_30'].' en los últimos 30 días'],
            ['Sesiones', $g['sesiones'], 'en total'],
            ['Repeticiones', $g['repeticiones'], 'en total'],
            ['Técnica promedio', $g['puntaje_promedio'] ?? '--', 'de 0 a 100'],
        ];
        $maxEj = max(1, (int) ($g['por_ejercicio']->max('sesiones') ?? 0));
        $maxSem = max(1, $g['max_semana']);
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 px-4">
            @include('admin._menu')

            @unless (auth()->user()->physicalProfile)
                <p class="rounded-md border border-gray-200 bg-white p-3 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    Como administrador no necesitas perfil físico. Si quieres entrenar tú también, <a class="text-blue-600 underline dark:text-blue-400" href="{{ route('perfil.crear') }}">complétalo aquí</a>.
                </p>
            @endunless

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($tarjetas as [$titulo, $valor, $nota])
                    <div class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $titulo }}</p>
                        <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $valor }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $nota }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Sesiones por ejercicio</h3>
                    @if ($g['por_ejercicio']->isEmpty())
                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Todavía no hay sesiones.</p>
                    @else
                        <ul class="mt-4 space-y-3">
                            @foreach ($g['por_ejercicio'] as $e)
                                <li>
                                    <div class="flex justify-between text-sm text-gray-700 dark:text-gray-300">
                                        <span>{{ \Illuminate\Support\Str::headline($e->ejercicio) }}@if ($e->ejercicio === $g['mas_usado']) <span class="ms-1 rounded bg-blue-100 px-1.5 py-0.5 text-xs text-blue-800 dark:bg-blue-900/40 dark:text-blue-200">más usado</span>@endif</span>
                                        <span>{{ $e->sesiones }} sesiones · {{ $e->repeticiones }} reps · técnica {{ $e->puntaje ?? '--' }}</span>
                                    </div>
                                    <div class="mt-1 h-2 rounded bg-gray-200 dark:bg-gray-700">
                                        <div class="h-2 rounded bg-blue-600" style="width: {{ round($e->sesiones / $maxEj * 100) }}%"></div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                <section class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Sesiones por semana (8 semanas)</h3>
                    <ul class="mt-4 space-y-2">
                        @foreach ($g['semanas'] as $s)
                            <li class="flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
                                <span class="w-12 shrink-0">{{ $s['etiqueta'] }}</span>
                                <div class="h-2 flex-1 rounded bg-gray-200 dark:bg-gray-700">
                                    <div class="h-2 rounded bg-blue-600" style="width: {{ round($s['sesiones'] / $maxSem * 100) }}%"></div>
                                </div>
                                <span class="w-8 shrink-0 text-end">{{ $s['sesiones'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400">Estas métricas son agregadas: no incluyen datos de salud ni el contenido de las consultas al asistente.</p>
        </div>
    </div>
</x-app-layout>
