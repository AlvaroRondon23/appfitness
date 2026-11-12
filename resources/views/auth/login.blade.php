<x-guest-layout>
    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700/50 backdrop-blur-sm text-gray-900 dark:text-gray-100">
        
        <!-- Encabezado del Formulario -->
        <div class="mb-6 text-center">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">¡Bienvenido de vuelta!</h2>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Ingresa tus credenciales para acceder a tu panel analítico de fitness.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4 text-emerald-600 dark:text-emerald-400" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Correo Electrónico</label>
                <x-text-input id="email" class="block mt-1 w-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-700 focus:ring-blue-500 focus:border-blue-500 rounded-xl" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 dark:text-red-400" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <label for="password" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Contraseña</label>
                <x-text-input id="password" class="block mt-1 w-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-700 focus:ring-blue-500 focus:border-blue-500 rounded-xl"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 dark:text-red-400" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded bg-gray-50 dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-300">Recordar sesión</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-6">
                @if (Route::has('password.request'))
                    <a class="text-xs text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-300 underline rounded-md focus:outline-none" href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif

                <x-primary-button class="ms-3 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition duration-200 shadow-md">
                    {{ __('Iniciar Sesión') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>