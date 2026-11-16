<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight">
            {{ __('Mi Panel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Notificación de actualización del perfil -->
            @if (session('status') === 'perfil-actualizado')
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 dark:bg-green-900/30 dark:border-green-800 dark:text-green-400 rounded-lg font-medium shadow-sm">
                    ✓ Tus indicadores físicos se han actualizado correctamente en el sistema.
                </div>
            @endif

            <!-- Encabezado de bienvenida y acciones CRUD -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-lg font-bold">¡Hola, {{ Auth::user()->name }}! 👋</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Aquí tienes el estado actual de tu diagnóstico físico e indicadores de rendimiento.</p>
                    </div>
                    
                    <div class="flex items-center space-x-3 w-full sm:w-auto">
                        <a href="/perfil-fisico/editar" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-md transition shadow-sm w-full sm:w-auto justify-center">
                            <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Actualizar Métricas
                        </a>

                        <form action="/physical-profile" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar por completo tu perfil físico? El sistema te pedirá ingresar tus datos nuevamente.');" class="w-full sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition shadow-sm w-full sm:w-auto justify-center">
                                <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-16v1a3 3 0 003 3h10M4 7h16" />
                                </svg>
                                Eliminar Perfil
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Indicadores básicos -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                
                <!-- Peso -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Peso Corporal</div>
                        <div class="mt-2 flex items-baseline">
                            <span class="text-3xl font-extrabold text-gray-900 dark:text-gray-100">{{ $perfil->weight }}</span>
                            <span class="ml-1 text-xl font-semibold text-gray-600 dark:text-gray-400">kg</span>
                        </div>
                    </div>
                </div>

                <!-- Estatura -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estatura</div>
                        <div class="mt-2 flex items-baseline">
                            <span class="text-3xl font-extrabold text-gray-900 dark:text-gray-100">{{ $perfil->height }}</span>
                            <span class="ml-1 text-xl font-semibold text-gray-600 dark:text-gray-400">cm</span>
                        </div>
                    </div>
                </div>

                <!-- Edad -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-purple-500">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Edad del Usuario</div>
                        <div class="mt-2 flex items-baseline">
                            <span class="text-3xl font-extrabold text-gray-900 dark:text-gray-100">
                                {{ \Carbon\Carbon::parse($perfil->birth_date)->age }}
                            </span>
                            <span class="ml-1 text-xl font-semibold text-gray-600 dark:text-gray-400">años</span>
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            Nacido el {{ \Carbon\Carbon::parse($perfil->birth_date)->format('d/m/Y') }}
                        </div>
                    </div>
                </div>

            </div>

            <!-- Cálculo de IMC, TMB y Macronutrientes -->
            @php
                // Cálculos centralizados en App\Services\PerfilCalculos (se pasan desde routes/web.php)
                $imc = $calculos['imc'];
                $edad = $calculos['edad'];
                $status = $calculos['categoria_imc'];

                $badgeBg = match ($status) {
                    'Bajo Peso' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400',
                    'Normal'    => 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400',
                    'Sobrepeso' => 'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-400',
                    default     => 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400',
                };

                // TMB (en reposo) y gasto diario estimado según el nivel de actividad
                $tmb = $calculos['tmb'];
                $tdee = $calculos['tdee'];

                // Distribución base (40% Carbohidratos, 30% Proteínas, 30% Grasas) sobre el gasto diario
                $carbsGramos = $calculos['carbs'];
                $proteinasGramos = $calculos['proteinas'];
                $grasasGramos = $calculos['grasas'];
            @endphp

            <!-- Métricas Calculadas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                
                <!-- Tarjeta IMC -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-amber-500 md:col-span-1 transition duration-200">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Índice de Masa Corporal (IMC)</div>
                        <div class="mt-2 flex flex-col gap-2">
                            <span class="text-3xl font-extrabold text-gray-900 dark:text-gray-100">{{ $imc }}</span>
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide uppercase shadow-sm {{ $badgeBg }}">
                                    {{ $status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta TMB y Macros -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-rose-500 md:col-span-2 transition duration-200">
                    <div class="p-6 flex flex-col justify-between h-full">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                            <div>
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gasto Energético Basal Estimado (TMB)</div>
                                <div class="mt-1 flex items-baseline">
                                    <span class="text-3xl font-extrabold text-gray-900 dark:text-gray-100">{{ number_format($tmb) }}</span>
                                    <span class="ml-1 text-xl font-semibold text-gray-600 dark:text-gray-400">kcal / día</span>
                                </div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Con tu nivel de actividad ({{ $perfil->activity_level }}): <b>{{ number_format($tdee) }} kcal / día</b>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Distribución Sugerida de Macronutrientes (sobre tu gasto diario)</div>
                            
                            <div class="w-full h-4 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden flex shadow-inner">
                                <div class="h-full bg-blue-500 transition-all duration-500" style="width: 40%;" title="Carbohidratos"></div>
                                <div class="h-full bg-emerald-500 transition-all duration-500" style="width: 30%;" title="Proteínas"></div>
                                <div class="h-full bg-amber-500 transition-all duration-500" style="width: 30%;" title="Grasas"></div>
                            </div>

                            <div class="flex justify-between items-center mt-3 text-xs font-medium">
                                <div class="flex items-center text-blue-600 dark:text-blue-400">
                                    <span class="w-2.5 h-2.5 bg-blue-500 rounded-full mr-1.5"></span>
                                    Carbs: <b>{{ $carbsGramos }}g</b> (40%)
                                </div>
                                <div class="flex items-center text-emerald-600 dark:text-emerald-400">
                                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full mr-1.5"></span>
                                    Prot: <b>{{ $proteinasGramos }}g</b> (30%)
                                </div>
                                <div class="flex items-center text-amber-600 dark:text-amber-400">
                                    <span class="w-2.5 h-2.5 bg-amber-500 rounded-full mr-1.5"></span>
                                    Grasas: <b>{{ $grasasGramos }}g</b> (30%)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 🤖 Módulo de Acceso al Entrenador de Postura con IA -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border-l-4 border-blue-600 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-xl mt-1">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Asistente de Postura con Inteligencia Artificial</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 max-w-2xl">
                            Monitorea tus sentadillas, bíceps, flexiones, saltos de tijera y elevaciones de rodillas en tiempo real utilizando la cámara de tu dispositivo. El sistema analiza ángulos articulares, estabilidad y repeticiones mediante MediaPipe.
                        </p>
                    </div>
                </div>
                
                <a href="{{ route('entrenamiento.detector') }}" class="w-full md:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-sm flex items-center justify-center gap-2 whitespace-nowrap">
                    Iniciar Sesión de Ejercicio
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Resumen de Rendimiento -->
            @php
                $nombresEj = ['sentadilla' => '🏋️‍♂️ Sentadillas', 'biceps' => '💪 Curl de Bíceps', 'flexion' => '🤸‍♂️ Flexiones', 'salto_tijera' => '🙆 Salto de Tijera', 'elevacion_rodillas' => '🦵 Elevación de Rodillas'];
                $horas = intdiv($stats['segundos'], 3600);
                $minutos = intdiv($stats['segundos'] % 3600, 60);
                $tiempoTotal = $horas > 0 ? "{$horas} h {$minutos} min" : "{$minutos} min";
                $tarjetas = [
                    ['Sesiones', $stats['sesiones'], 'text-blue-600 dark:text-blue-400'],
                    ['Repeticiones totales', number_format($stats['repeticiones']), 'text-emerald-600 dark:text-emerald-400'],
                    ['Tiempo entrenado', $tiempoTotal, 'text-purple-600 dark:text-purple-400'],
                    ['Racha actual', $stats['racha'] . ($stats['racha'] === 1 ? ' día' : ' días'), 'text-amber-600 dark:text-amber-400'],
                ];
                $meta = $stats['meta'];
                $faltan = max(0, $meta['objetivo'] - $meta['hechas']);
            @endphp
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl p-6 border border-gray-100 dark:border-gray-700/50 mb-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Resumen de Rendimiento
                </h3>

                @if ($stats['sesiones'] > 0)
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        @foreach ($tarjetas as [$etiqueta, $valor, $color])
                            <div class="rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/50 p-4">
                                <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">{{ $etiqueta }}</div>
                                <div class="mt-1 text-2xl font-extrabold {{ $color }}">{{ $valor }}</div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Meta semanal -->
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/50 p-4 mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Meta de esta semana</div>
                            <div class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $meta['hechas'] }} / {{ $meta['objetivo'] }} sesiones</div>
                        </div>
                        <div class="w-full h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 {{ $meta['porcentaje'] >= 100 ? 'bg-emerald-500' : 'bg-blue-500' }}" style="width: {{ $meta['porcentaje'] }}%"></div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            @if ($faltan === 0)
                                ¡Meta cumplida esta semana! 🎉
                            @else
                                Te {{ $faltan === 1 ? 'falta 1 sesión' : "faltan {$faltan} sesiones" }} para cumplir tu meta. La semana se reinicia el lunes.
                            @endif
                        </p>
                    </div>

                    <!-- Gráficas con Chart.js -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">Repeticiones por semana</div>
                            <div class="relative h-56">
                                <canvas id="grafica-semanas"></canvas>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-baseline mb-3">
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Evolución de la técnica</div>
                                @if ($stats['puntaje_promedio'] !== null)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Promedio general: <b class="text-gray-800 dark:text-gray-200">{{ $stats['puntaje_promedio'] }}/100</b></div>
                                @endif
                            </div>
                            @if (count($stats['evolucion_puntaje']) > 0)
                                <div class="relative h-56">
                                    <canvas id="grafica-tecnica"></canvas>
                                </div>
                            @else
                                <div class="h-56 flex items-center justify-center text-center text-sm text-gray-400 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-dashed border-gray-200 dark:border-gray-700 px-6">
                                    Aún no hay puntajes de técnica en los últimos 30 días.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Sesiones por ejercicio -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                        <div>
                            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">Sesiones por ejercicio</div>
                            <div class="relative h-56">
                                <canvas id="grafica-ejercicios"></canvas>
                            </div>
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">Detalle por ejercicio</div>
                            <div class="space-y-2">
                                @foreach ($stats['por_ejercicio'] as $e)
                                    <div class="flex items-center justify-between gap-3 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/50 px-4 py-3 text-sm">
                                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $nombresEj[$e->ejercicio] ?? ucfirst($e->ejercicio) }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 text-right">
                                            {{ $e->sesiones }} {{ $e->sesiones == 1 ? 'sesión' : 'sesiones' }} ·
                                            <b class="text-gray-800 dark:text-gray-200">{{ number_format($e->repeticiones) }}</b> reps ·
                                            mejor: <b class="text-gray-800 dark:text-gray-200">{{ $e->mejor }}</b>
                                            @if ($e->puntaje !== null)
                                                · técnica: <b class="text-gray-800 dark:text-gray-200">{{ (int) $e->puntaje }}</b>
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                Días activos en los últimos 30 días: <b>{{ $stats['dias_activos'] }}</b> · Mejor sesión: <b>{{ $stats['mejor'] }}</b> repeticiones
                            </p>
                        </div>
                    </div>
                @else
                    <!-- Estado vacío -->
                    <div class="text-center py-10 px-6 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                        <div class="text-4xl mb-3">🏋️</div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200">Todavía no tienes sesiones registradas</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-5">Completa tu primera sesión con el detector de postura y aquí verás tus repeticiones, tu técnica y tu progreso semanal.</p>
                        <a href="{{ route('entrenamiento.detector') }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition shadow-sm">
                            Empezar a entrenar
                        </a>
                    </div>
                @endif
            </div>

            
<!-- Historial de Sesiones Recientes -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-2xl p-6 border border-gray-100 dark:border-gray-700/50 mb-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Historial de Sesiones de Entrenamiento
                </h3>

                @if(isset($sesiones) && $sesiones->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                            <thead class="bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-gray-400 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3 rounded-l-xl">Ejercicio</th>
                                    <th class="px-4 py-3">Repeticiones</th>
                                    <th class="px-4 py-3">Técnica</th>
                                    <th class="px-4 py-3">Duración</th>
                                    <th class="px-4 py-3 rounded-r-xl">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($sesiones as $sesion)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition">
                                        <td class="px-4 py-3 font-bold text-gray-800 dark:text-white">
                                            {{ $nombresEj[$sesion->ejercicio] ?? ucfirst($sesion->ejercicio) }}
                                        </td>
                                        <td class="px-4 py-3 font-mono font-bold text-blue-600 dark:text-blue-400">
                                            {{ $sesion->repeticiones }} reps
                                        </td>
                                        <td class="px-4 py-3 font-mono">
                                            @if ($sesion->puntaje !== null)
                                                <span class="{{ $sesion->puntaje >= 85 ? 'text-emerald-600 dark:text-emerald-400' : ($sesion->puntaje >= 70 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }} font-bold">{{ $sesion->puntaje }}/100</span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 font-mono">
                                            {{ intdiv($sesion->tiempo_segundos, 60) }}m {{ $sesion->tiempo_segundos % 60 }}s
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-400">
                                            {{ $sesion->created_at->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400 text-sm bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                        Aún no tienes entrenamientos registrados. ¡Usa el asistente de IA para completar tu primera sesión y guardar los resultados!
                    </div>
                @endif
            </div>

            <!-- Optimizador / Simulador Nutricional -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                            </svg>
                            Optimizador Algorítmico de Cargas Nutricionales
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Modifica las variables en tiempo real para simular proyecciones de rendimiento.</p>
                    </div>
                    
                    <button onclick="window.print()" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-200 font-semibold text-xs rounded-xl transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exportar Ficha Actual
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Controles del simulador -->
                    <div class="space-y-4 bg-gray-50 dark:bg-gray-900/40 p-4 rounded-xl border border-gray-100 dark:border-gray-800/60">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase mb-2">Objetivo Metabólico</label>
                            <select id="sim-objetivo" onchange="calcularMetricas()" class="w-full text-sm bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-blue-500">
                                <option value="definicion">Definición Estricta (Déficit)</option>
                                <option value="mantenimiento" selected>Mantenimiento Normocalórico</option>
                                <option value="volumen">Superávit Funcional (Volumen)</option>
                            </select>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">Duración Entrenamiento</label>
                                <span id="val-tiempo" class="text-xs font-mono font-bold text-blue-600 dark:text-blue-400">60 min</span>
                            </div>
                            <input id="sim-tiempo" type="range" min="30" max="180" step="15" value="60" oninput="calcularMetricas()" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase mb-1">Peso Referencial de Trabajo</label>
                            <div class="relative rounded-xl shadow-sm">
                                <input type="number" id="sim-peso" value="{{ $perfil->weight }}" oninput="calcularMetricas()" class="w-full text-sm bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-blue-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-xs text-gray-400 font-mono">kg</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resultados dinámicos -->
                    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 dark:from-blue-950/20 dark:to-transparent p-5 rounded-xl border border-blue-100 dark:border-blue-900/30 flex flex-col justify-between">
                            <div>
                                <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Tasa Hidratación</span>
                                <div class="text-3xl font-black text-gray-800 dark:text-white mt-2" id="res-agua">2.85 L</div>
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-4">Calculado según gasto base más tasa de sudoración por minuto activo.</p>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-purple-100/50 dark:from-purple-950/20 dark:to-transparent p-5 rounded-xl border border-purple-100 dark:border-purple-900/30 flex flex-col justify-between">
                            <div>
                                <span class="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Proteína Óptima</span>
                                <div class="text-3xl font-black text-gray-800 dark:text-white mt-2" id="res-proteina">150 g</div>
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-4" id="desc-proteina">Ratio ajustado a 2.0g/kg para preservación tisular.</p>
                        </div>

                        <div class="bg-gradient-to-br from-rose-50 to-rose-100/50 dark:from-rose-950/20 dark:to-transparent p-5 rounded-xl border border-rose-100 dark:border-rose-900/30 flex flex-col justify-between">
                            <div>
                                <span class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Umbral Glucogénico</span>
                                <div class="text-3xl font-black text-gray-800 dark:text-white mt-2" id="res-carbo">300 g</div>
                            </div>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-4" id="desc-carbo">Suministro energético ajustado para sostener la sesión de entrenamiento.</p>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Gráficas del panel (Chart.js se carga desde resources/js/app.js)
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') return;

            const colorTexto = '#9ca3af';
            const colorGrilla = 'rgba(156, 163, 175, 0.2)';
            Chart.defaults.color = colorTexto;
            Chart.defaults.font.family = 'inherit';

            const nombres = { sentadilla: 'Sentadillas', biceps: 'Curl de bíceps', flexion: 'Flexiones', salto_tijera: 'Salto de tijera', elevacion_rodillas: 'Elevación de rodillas' };
            const semanas = @json($stats['semanas']);
            const tecnica = @json($stats['evolucion_puntaje']);
            const porEjercicio = @json($stats['por_ejercicio']);

            const ejes = (extra = {}) => ({
                x: { grid: { display: false } },
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: colorGrilla }, ...extra },
            });

            const lienzoSemanas = document.getElementById('grafica-semanas');
            if (lienzoSemanas) {
                new Chart(lienzoSemanas, {
                    type: 'bar',
                    data: {
                        labels: semanas.map(s => s.etiqueta),
                        datasets: [{ label: 'Repeticiones', data: semanas.map(s => s.reps), backgroundColor: '#3b82f6', borderRadius: 4 }],
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: ejes() },
                });
            }

            const lienzoTecnica = document.getElementById('grafica-tecnica');
            if (lienzoTecnica) {
                new Chart(lienzoTecnica, {
                    type: 'line',
                    data: {
                        labels: tecnica.map(t => t.etiqueta),
                        datasets: [{ label: 'Técnica', data: tecnica.map(t => t.puntaje), borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.15)', fill: true, tension: 0.3, pointRadius: 3 }],
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: ejes({ max: 100 }) },
                });
            }

            const lienzoEjercicios = document.getElementById('grafica-ejercicios');
            if (lienzoEjercicios) {
                new Chart(lienzoEjercicios, {
                    type: 'doughnut',
                    data: {
                        labels: porEjercicio.map(e => nombres[e.ejercicio] ?? e.ejercicio),
                        datasets: [{ data: porEjercicio.map(e => e.sesiones), backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#a855f7', '#ef4444'], borderWidth: 0 }],
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
                });
            }
        });
    </script>

    <script>
        function calcularMetricas() {
            const objetivo = document.getElementById('sim-objetivo').value;
            const tiempo = parseInt(document.getElementById('sim-tiempo').value);
            const peso = parseFloat(document.getElementById('sim-peso').value) || 70;
    
            document.getElementById('val-tiempo').innerText = `${tiempo} min`;
    
            let litrosAgua = ((peso * 35) + (tiempo * 12)) / 1000;
            
            let factorProteina = 1.8;
            let factorCarbo = 4.0;
    
            if (objetivo === 'definicion') {
                factorProteina = 2.3;
                factorCarbo = 2.5;
                document.getElementById('desc-proteina').innerText = "Ratio elevado a 2.3g/kg para protección muscular en déficit.";
                document.getElementById('desc-carbo').innerText = "Carbohidratos restringidos para promover la lipólisis energética.";
            } else if (objetivo === 'mantenimiento') {
                factorProteina = 2.0;
                factorCarbo = 4.0;
                document.getElementById('desc-proteina').innerText = "Ratio balanceado a 2.0g/kg para recambio proteico diario.";
                document.getElementById('desc-carbo').innerText = "Aporte normocalórico ideal para balance energético neutro.";
            } else if (objetivo === 'volumen') {
                factorProteina = 1.8;
                factorCarbo = 5.5;
                document.getElementById('desc-proteina').innerText = "Ratio optimizado a 1.8g/kg apoyado por superávit calórico.";
                document.getElementById('desc-carbo').innerText = "Carga glucogénica maximizada para promover el anabolismo muscular.";
            }
    
            if (tiempo > 90) {
                factorCarbo += 0.5;
            }
    
            let totalProteina = Math.round(peso * factorProteina);
            let totalCarbo = Math.round(peso * factorCarbo);
    
            document.getElementById('res-agua').innerText = `${litrosAgua.toFixed(2)} L`;
            document.getElementById('res-proteina').innerText = `${totalProteina} g`;
            document.getElementById('res-carbo').innerText = `${totalCarbo} g`;
        }
    
        document.addEventListener('DOMContentLoaded', calcularMetricas);
    </script>
</x-app-layout>
