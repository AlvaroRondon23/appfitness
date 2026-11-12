<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Planificación Funcional de Sesiones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Analizador Lógico de Frecuencia de Carga de Entrenamiento
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Distribuye tus estímulos semanales. El sistema auditará de manera síncrona si respetas los tiempos biológicos de recuperación.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <!-- Selectores de Días -->
                    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-3 bg-gray-50 dark:bg-gray-900/60 rounded-xl border border-gray-100 dark:border-gray-800">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">Lunes (Día 1)</span>
                            <select id="split-d1" onchange="auditarEntrenamiento()" class="w-full text-xs mt-1 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 rounded-lg focus:ring-emerald-500">
                                <option value="empuje">Empuje (Pecho/Hombro/Tríceps)</option>
                                <option value="traccion">Tracción (Espalda/Bíceps)</option>
                                <option value="pierna">Pierna Completa</option>
                                <option value="descanso">Descanso Total</option>
                            </select>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-900/60 rounded-xl border border-gray-100 dark:border-gray-800">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">Martes (Día 2)</span>
                            <select id="split-d2" onchange="auditarEntrenamiento()" class="w-full text-xs mt-1 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 rounded-lg focus:ring-emerald-500">
                                <option value="empuje">Empuje (Pecho/Hombro/Tríceps)</option>
                                <option value="traccion" selected>Tracción (Espalda/Bíceps)</option>
                                <option value="pierna">Pierna Completa</option>
                                <option value="descanso">Descanso Total</option>
                            </select>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-900/60 rounded-xl border border-gray-100 dark:border-gray-800">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">Miércoles (Día 3)</span>
                            <select id="split-d3" onchange="auditarEntrenamiento()" class="w-full text-xs mt-1 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 rounded-lg focus:ring-emerald-500">
                                <option value="empuje">Empuje (Pecho/Hombro/Tríceps)</option>
                                <option value="traccion">Tracción (Espalda/Bíceps)</option>
                                <option value="pierna" selected>Pierna Completa</option>
                                <option value="descanso">Descanso Total</option>
                            </select>
                        </div>
                    </div>

                    <!-- Consola Informativa de Auditoría Estática/Dinámica -->
                    <div id="consola-alerta" class="p-4 rounded-xl border flex flex-col justify-between transition-all duration-300 bg-emerald-50 dark:bg-emerald-950/20 border-emerald-100 dark:border-emerald-900/40 text-emerald-800 dark:text-emerald-400">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider block mb-1">Informe de Optimización</span>
                            <p id="consola-texto" class="text-xs leading-relaxed">
                                Distribución óptima detectada. Se respeta el principio biológico de las 48 horas de recuperación para grupos musculares sinergistas.
                            </p>
                        </div>
                        <div class="text-[10px] font-mono text-right mt-2 opacity-70">Heurística de Carga v1.1</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Script de Evaluación de Reglas de Entrenamiento -->
    <script>
        function auditarEntrenamiento() {
            const d1 = document.getElementById('split-d1').value;
            const d2 = document.getElementById('split-d2').value;
            const d3 = document.getElementById('split-d3').value;
            
            const consola = document.getElementById('consola-alerta');
            const texto = document.getElementById('consola-texto');

            // Regla heurística: El mismo estímulo o solapamiento sinergista en días consecutivos genera sobrecarga
            if ((d1 === d2 && d1 !== 'descanso') || (d2 === d3 && d2 !== 'descanso')) {
                consola.className = "p-4 rounded-xl border flex flex-col justify-between transition-all duration-300 bg-rose-50 dark:bg-rose-950/20 border-rose-100 dark:border-rose-900/40 text-rose-800 dark:text-rose-400";
                texto.innerText = "Alerta crítica: Se detectó un solapamiento perjudicial. Entrenar cadenas musculares idénticas de forma consecutiva inhibe la resíntesis de glucógeno y eleva el riesgo de fatiga del sistema nervioso central (SNC).";
            } else {
                consola.className = "p-4 rounded-xl border flex flex-col justify-between transition-all duration-300 bg-emerald-50 dark:bg-emerald-950/20 border-emerald-100 dark:border-emerald-900/40 text-emerald-800 dark:text-emerald-400";
                texto.innerText = "Distribución de microciclo óptima. El plan estructurado permite la regeneración tisular miofibrilar completa, promoviendo el proceso fisiológico de la supercompensación.";
            }
        }
        // Inicializar al renderizar el DOM
        document.addEventListener('DOMContentLoaded', auditarEntrenamiento);
    </script>
</x-app-layout>