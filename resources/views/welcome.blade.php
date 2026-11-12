<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>AppFitness - Diagnóstico Inteligente</title>
        <!-- Fuentes y Tailwind (Aprovechando la configuración de Breeze) -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <!-- Fallback CDN de Tailwind por si acaso para asegurar estilos en la vista suelta -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            // Configuración para que Tailwind reconozca el modo oscuro nativo
            tailwind.config = { darkMode: 'media' }
        </script>
    </head>
    <body class="antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100 flex flex-col min-h-screen justify-between font-['Figtree']">

        <!-- Barra de Navegación Superior -->
        <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
            <!-- Marca de la App -->
            <div class="flex items-center space-x-2">
                <div class="bg-gradient-to-br from-blue-500 to-rose-500 p-2 rounded-lg shadow-md">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-xl font-black tracking-wider bg-gradient-to-r from-blue-600 via-purple-600 to-rose-600 bg-clip-text text-transparent uppercase">
                    AppFitness<span class="text-xs font-mono lowercase text-gray-500"></span>
                </span>
            </div>

            <!-- Menú de Autenticación Dinámico de Laravel -->
            @if (Route::has('login'))
                <nav class="flex space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-800 text-sm font-semibold rounded-xl hover:bg-gray-300 dark:hover:bg-gray-700 transition">
                            Ir al Panel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2 transition">
                            Ingresar
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition">
                                Registrarse
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <!-- Sección Principal (Hero Section) -->
        <main class="max-w-4xl mx-auto text-center px-6 py-12 flex flex-col items-center justify-center flex-grow">
            <!-- Badge conceptual -->
            

            <!-- Título de Impacto -->
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight leading-none mb-6">
                Rendimiento Físico Mediante <span class="bg-gradient-to-r from-blue-500 via-indigo-500 to-rose-500 bg-clip-text text-transparent">Inteligencia Artificial</span>
            </h1>

            <!-- Subtítulo -->
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mb-10 leading-relaxed">
                Una plataforma informática avanzada orientada al diagnóstico, seguimiento algorítmico y prescripción inteligente de macronutrientes e indicadores bioestadísticos.
            </p>

            <!-- Botón de Llamado a la Acción Central -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center w-full max-w-md">
                @auth
                    <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200 text-center">
                        Acceder a mi Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200 text-center">
                        Comenzar Diagnóstico
                    </a>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 font-bold rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-200 text-center">
                        Tengo una cuenta
                    </a>
                @endauth
            </div>
        </main>

        <!-- Pie de Página -->
        <footer class="w-full border-t border-gray-100 dark:border-gray-800/50 py-6 text-center text-xs text-gray-400 dark:text-gray-500">
            &copy; 2026 AppFitness &bull; Desarrollado como Plataforma de Ingeniería Informática. Todos los derechos reservados.
        </footer>

    </body>
</html>