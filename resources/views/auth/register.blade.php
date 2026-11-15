<x-guest-layout>
    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700/50 backdrop-blur-sm text-gray-900 dark:text-gray-100">
        
        <!-- Encabezado del Formulario -->
        <div class="mb-6 text-center">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Crear Cuenta Física</h2>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Regístrate para comenzar el seguimiento automatizado con inteligencia artificial.</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Nombre Completo</label>
                <x-text-input id="name" class="block mt-1 w-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-700 focus:ring-blue-500 focus:border-blue-500 rounded-xl" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-600 dark:text-red-400" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <label for="email" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Correo Electrónico</label>
                <x-text-input id="email" class="block mt-1 w-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-700 focus:ring-blue-500 focus:border-blue-500 rounded-xl" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 dark:text-red-400" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <label for="password" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Contraseña</label>
                <x-text-input id="password" class="block mt-1 w-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-700 focus:ring-blue-500 focus:border-blue-500 rounded-xl"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 dark:text-red-400" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Confirmar Contraseña</label>
                <x-text-input id="password_confirmation" class="block mt-1 w-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-700 focus:ring-blue-500 focus:border-blue-500 rounded-xl"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-600 dark:text-red-400" />
            </div>

            <!-- Consentimiento -->
            <div class="mt-5">
                <label for="privacidad" class="flex items-start gap-2 text-xs leading-relaxed text-gray-700 dark:text-gray-300">
                    <input id="privacidad" name="privacidad" type="checkbox" value="1" required @checked(old('privacidad'))
                           class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span>
                        Acepto la
                        <a href="{{ route('privacidad') }}" target="_blank" rel="noopener" class="underline text-blue-600 hover:text-blue-700 dark:text-blue-400">política de privacidad</a>
                        y el
                        <a href="{{ route('privacidad') }}#aviso-medico" target="_blank" rel="noopener" class="underline text-blue-600 hover:text-blue-700 dark:text-blue-400">aviso médico</a>,
                        incluido el envío de mi perfil y mis preguntas al asistente de IA (Gemini).
                    </span>
                </label>
                <x-input-error :messages="$errors->get('privacidad')" class="mt-2 text-red-600 dark:text-red-400" />
            </div>

            <div class="flex items-center justify-between mt-6">
                <a class="text-xs text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-300 underline rounded-md focus:outline-none" href="{{ route('login') }}">
                    ¿Ya estás registrado?
                </a>

                <x-primary-button class="ms-3 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition duration-200 shadow-md">
                    {{ __('Registrarse') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>