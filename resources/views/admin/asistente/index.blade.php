<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Administración · Asistente IA</h2>
    </x-slot>

    @php
        $etiquetas = \App\Models\RegistroIa::RESULTADOS;
        $tarjetas = [
            ['Consultas hoy', $hoy, ''],
            ['Últimos 30 días', $total, $usuarios.' usuarios distintos'],
            ['Tasa de éxito', $exito !== null ? $exito.' %' : '--', 'consultas correctas'],
            ['Duración media', $duracionOk !== null ? number_format($duracionOk / 1000, 1).' s' : '--', 'de las correctas'],
        ];
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 px-4">
            @include('admin._menu')

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
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
                    <h3 class="font-semibold text-gray-900 dark:text-white">Resultados (30 días)</h3>
                    @forelse ($porResultado as $clave => $n)
                        <div class="mt-2 flex justify-between text-sm text-gray-700 dark:text-gray-300">
                            <span>{{ $etiquetas[$clave] ?? $clave }}</span><span>{{ $n }}</span>
                        </div>
                    @empty
                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Sin consultas todavía.</p>
                    @endforelse
                </section>

                <section class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Modelos que respondieron (30 días)</h3>
                    @forelse ($porModelo as $m)
                        <div class="mt-2 flex justify-between text-sm text-gray-700 dark:text-gray-300">
                            <span>{{ $m->modelo }}</span><span>{{ $m->total }} · {{ number_format($m->ms / 1000, 1) }} s</span>
                        </div>
                    @empty
                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Sin respuestas correctas todavía.</p>
                    @endforelse
                </section>
            </div>

            <section class="rounded-lg bg-white p-5 shadow-sm dark:bg-gray-800">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Registro reciente</h3>
                    <form method="GET" action="{{ route('admin.asistente.index') }}" class="flex items-center gap-2">
                        <label for="resultado" class="sr-only">Filtrar por resultado</label>
                        <select id="resultado" name="resultado" onchange="this.form.submit()"
                                class="rounded-md border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            <option value="">Todos los resultados</option>
                            @foreach ($etiquetas as $clave => $texto)
                                <option value="{{ $clave }}" @selected($filtro === $clave)>{{ $texto }}</option>
                            @endforeach
                        </select>
                        <noscript><button type="submit" class="text-sm underline">Filtrar</button></noscript>
                    </form>
                </div>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm text-gray-700 dark:text-gray-300">
                        <thead class="text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="py-2 pe-4 text-start">Fecha</th>
                                <th class="py-2 pe-4 text-start">Usuario</th>
                                <th class="py-2 pe-4 text-start">Resultado</th>
                                <th class="py-2 pe-4 text-start">Modelo</th>
                                <th class="py-2 pe-4 text-end">HTTP</th>
                                <th class="py-2 pe-4 text-end">Duración</th>
                                <th class="py-2 pe-4 text-end">Car. pregunta</th>
                                <th class="py-2 text-end">Car. respuesta</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($registros as $r)
                                <tr>
                                    <td class="py-2 pe-4">{{ $r->created_at?->format('d/m/Y H:i') }}</td>
                                    <td class="py-2 pe-4">{{ $r->user?->name ?? '—' }}</td>
                                    <td class="py-2 pe-4">{{ $etiquetas[$r->resultado] ?? $r->resultado }}</td>
                                    <td class="py-2 pe-4">{{ $r->modelo ?? '—' }}</td>
                                    <td class="py-2 pe-4 text-end">{{ $r->http_status ?? '—' }}</td>
                                    <td class="py-2 pe-4 text-end">{{ number_format($r->duracion_ms / 1000, 1) }} s</td>
                                    <td class="py-2 pe-4 text-end">{{ $r->caracteres_pregunta }}</td>
                                    <td class="py-2 text-end">{{ $r->caracteres_respuesta ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="py-6 text-center text-gray-500 dark:text-gray-400">No hay registros.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $registros->links() }}</div>
            </section>

            <p class="text-xs text-gray-500 dark:text-gray-400">Solo se muestran datos técnicos. El contenido de las preguntas y respuestas no se guarda aquí ni se muestra a los administradores.</code>.</p>
        </div>
    </div>
</x-app-layout>
