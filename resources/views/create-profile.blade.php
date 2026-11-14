<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Completa tu Perfil Físico') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-600 mb-6 text-sm">
                    Para ofrecerte un diagnóstico preciso y recomendaciones personalizadas seguras, necesitamos conocer tus datos biométricos actuales.
                </p>

                <form method="POST" action="/physical-profile">
                    @csrf
                        @if ($errors->any())
                            <div class="mb-4 p-3 rounded-md bg-red-50 border border-red-200 text-sm text-red-700">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                    <!-- Peso y Estatura -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <x-input-label for="weight" :value="__('Peso Actual (kg)')" />
                            <x-text-input id="weight" class="block mt-1 w-full" type="number" step="0.01" name="weight" required autofocus placeholder="Ej: 70.50" />
                        </div>

                        <div>
                            <x-input-label for="height" :value="__('Estatura (cm)')" />
                            <x-text-input id="height" class="block mt-1 w-full" type="number" name="height" required placeholder="Ej: 175" />
                        </div>
                    </div>

                    <!-- Fecha de Nacimiento y Género -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <x-input-label for="birth_date" :value="__('Fecha de Nacimiento')" />
                            <x-text-input id="birth_date" class="block mt-1 w-full" type="date" name="birth_date" required />
                        </div>

                        <div>
                            <x-input-label for="gender" :value="__('Género')" />
                            <select id="gender" name="gender" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>

                    <!-- Nivel de Actividad -->
                    <div class="mb-4">
                        <x-input-label for="activity_level" :value="__('Nivel de Actividad Física')" />
                        <select id="activity_level" name="activity_level" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="Sedentario">Sedentario (Poco o ningún ejercicio)</option>
                            <option value="Moderado">Moderado (Ejercicio 2-3 veces por semana)</option>
                            <option value="Activo">Activo (Entrenamiento intenso diario)</option>
                        </select>
                    </div>

                    <!-- Condiciones Médicas / Lesiones -->
                    <div class="mb-6">
                        <x-input-label for="medical_conditions" :value="__('¿Posees alguna lesión o condición médica? (Opcional)')" />
                        <textarea id="medical_conditions" name="medical_conditions" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Ej: Lesión de menisco en rodilla izquierda, hipertensión, etc."></textarea>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button>
                            {{ __('Guardar Datos y Continuar') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>