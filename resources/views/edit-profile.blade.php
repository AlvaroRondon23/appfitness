<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modificar Indicadores Físicos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-sm text-gray-600 mb-6">Actualiza tus datos para que el módulo analítico recalcule tus métricas físicas.</p>

                    <form method="POST" action="/physical-profile">
                        @csrf
                        @method('PUT') <!-- Crucial para decirle a Laravel que es una actualización -->

                        <!-- Peso -->
                        <div class="mb-4">
                            <label for="weight" class="block text-sm font-medium text-gray-700">Peso Corporal (kg)</label>
                            <input type="number" step="0.1" name="weight" id="weight" value="{{ old('weight', $perfil->weight) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Estatura -->
                        <div class="mb-4">
                            <label for="height" class="block text-sm font-medium text-gray-700">Estatura (cm)</label>
                            <input type="number" name="height" id="height" value="{{ old('height', $perfil->height) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Fecha de Nacimiento -->
                        <div class="mb-6">
                            <label for="birth_date" class="block text-sm font-medium text-gray-700">Fecha de Nacimiento</label>
                            <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $perfil->birth_date) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="flex items-center justify-end space-x-3">
                            <a href="/dashboard" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium rounded-md transition">
                                Cancelar
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition shadow-sm">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>