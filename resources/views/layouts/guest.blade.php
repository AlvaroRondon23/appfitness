<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AppFitness') }}</title>

        <!-- Fuentes -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Respetar el modo elegido (mismo criterio que el resto de la app) -->
        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-8 bg-gray-100 dark:bg-gray-900">
            <a href="/" class="rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500" aria-label="AppFitness, ir al inicio">
                <x-marca />
            </a>

            <!-- La tarjeta se mantiene clara en ambos modos para que los formularios de Breeze se lean bien -->
            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white text-gray-900 shadow-md overflow-hidden rounded-xl dark:ring-1 dark:ring-gray-700">
                {{ $slot }}
            </div>

            <p class="mt-6 max-w-md text-center text-xs text-gray-500 dark:text-gray-400">
                El video de tu cámara se procesa en tu navegador y no se envía a ningún servidor.
            </p>
        </div>
    </body>
</html>
