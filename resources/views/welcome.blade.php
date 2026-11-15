<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="AppFitness cuenta tus repeticiones y puntúa tu técnica con la cámara de tu dispositivo. Sentadillas, curl de bíceps y flexiones.">
        <title>AppFitness: entrena con la técnica correcta</title>

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

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white text-gray-900 dark:bg-gray-900 dark:text-gray-100">

        <!-- Barra superior -->
        <header class="mx-auto flex max-w-6xl items-center justify-between px-6 py-5">
            <a href="/" class="rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500" aria-label="AppFitness, inicio">
                <x-marca />
            </a>

            <nav class="flex items-center gap-1 sm:gap-3" aria-label="Acceso">
                <button type="button" onclick="toggleDarkMode()" title="Cambiar entre modo claro y oscuro" aria-label="Cambiar entre modo claro y oscuro"
                        class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                    <svg class="hidden h-5 w-5 dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                    <svg class="block h-5 w-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                </button>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">
                            Ir a Mi Panel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 transition hover:text-blue-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:text-gray-300 dark:hover:text-blue-400">
                            Ingresar
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">
                                Crear cuenta
                            </a>
                        @endif
                    @endauth
                @endif
            </nav>
        </header>

        <main>
            <!-- Portada -->
            <section class="mx-auto grid max-w-6xl items-center gap-12 px-6 pb-16 pt-8 lg:grid-cols-2 lg:gap-16 lg:pb-24 lg:pt-14">
                <div>
                    <h1 class="text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl">
                        Entrena frente a la cámara y corrige tu técnica al instante
                    </h1>

                    <p class="mt-6 max-w-xl text-lg leading-relaxed text-gray-600 dark:text-gray-300">
                        AppFitness cuenta tus repeticiones de sentadillas, curl de bíceps y flexiones, y puntúa tu técnica de 0 a 100 en cada sesión.
                    </p>
                    <p class="mt-3 max-w-xl text-base leading-relaxed text-gray-600 dark:text-gray-300">
                        El análisis se hace en tu navegador: el video no sale de tu dispositivo.
                    </p>

                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        @auth
                            <a href="{{ route('entrenamiento.detector') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">
                                Empezar a entrenar
                            </a>
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-3 text-base font-semibold text-gray-800 transition hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:text-gray-100 dark:hover:bg-gray-800">
                                Ver Mi Panel
                            </a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">
                                    Crear mi cuenta
                                </a>
                            @endif
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-3 text-base font-semibold text-gray-800 transition hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:text-gray-100 dark:hover:bg-gray-800">
                                    Ya tengo cuenta
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Vista de ejemplo del detector -->
                <figure class="mx-auto w-full max-w-sm lg:max-w-md">
                    <div class="relative aspect-[4/5] overflow-hidden rounded-2xl bg-gray-900 shadow-xl ring-1 ring-gray-800">
                        <svg viewBox="0 0 320 400" class="absolute inset-0 h-full w-full" role="img" aria-label="Figura de una persona haciendo una sentadilla con el ángulo de la rodilla marcado en 92 grados">
                            <!-- Esquinas de encuadre -->
                            <g fill="none" stroke="#4b5563" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 44 V14 H44" />
                                <path d="M276 14 H306 V44" />
                                <path d="M14 356 V386 H44" />
                                <path d="M276 386 H306 V356" />
                            </g>

                            <g transform="translate(0,16)">
                                <!-- Suelo -->
                                <line x1="56" y1="362" x2="264" y2="362" stroke="#374151" stroke-width="2" />

                                <!-- Cuerpo -->
                                <g fill="none" stroke="#3b82f6" stroke-width="5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="118,362 130,350 178,362" />
                                    <polyline points="130,350 185,265 115,215 150,115" />
                                    <line x1="150" y1="115" x2="156" y2="96" />
                                    <circle cx="158" cy="78" r="20" />
                                </g>
                                <polyline points="150,115 192,142 228,126" fill="none" stroke="#60a5fa" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />

                                <!-- Ángulo de la rodilla -->
                                <path d="M169.8 288.5 A28 28 0 0 1 162.2 248.7" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" />
                                <rect x="206" y="250" width="92" height="26" rx="13" fill="rgba(16,185,129,0.15)" stroke="#10b981" />
                                <text x="252" y="268" text-anchor="middle" fill="#6ee7b7" font-size="13" font-weight="600" font-family="inherit">Rodilla 92°</text>

                                <!-- Articulaciones -->
                                <g fill="#ffffff" stroke="#3b82f6" stroke-width="3">
                                    <circle cx="130" cy="350" r="6" />
                                    <circle cx="185" cy="265" r="7" stroke="#10b981" />
                                    <circle cx="115" cy="215" r="6" />
                                    <circle cx="150" cy="115" r="6" />
                                    <circle cx="192" cy="142" r="5" />
                                    <circle cx="228" cy="126" r="5" />
                                </g>
                            </g>
                        </svg>

                        <!-- Contadores -->
                        <div class="absolute left-4 top-4 rounded-lg bg-gray-800/80 px-3 py-2 text-white backdrop-blur">
                            <div class="text-xs text-gray-400">Repeticiones</div>
                            <div class="text-2xl font-bold leading-none">7</div>
                        </div>
                        <div class="absolute right-4 top-4 rounded-lg bg-gray-800/80 px-3 py-2 text-right text-white backdrop-blur">
                            <div class="text-xs text-gray-400">Técnica</div>
                            <div class="text-2xl font-bold leading-none text-emerald-400">88<span class="text-sm font-medium text-gray-400">/100</span></div>
                        </div>
                    </div>
                    <figcaption class="mt-3 text-center text-xs text-gray-500 dark:text-gray-400">
                        Vista de ejemplo del detector de postura.
                    </figcaption>
                </figure>
            </section>

            <!-- Qué puedes hacer -->
            <section class="border-t border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/40">
                <div class="mx-auto max-w-6xl px-6 py-16 lg:py-20">
                    <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Qué puedes hacer en AppFitness</h2>

                    <div class="mt-10 grid gap-10 md:grid-cols-3">
                        <div class="border-t-2 border-blue-600 pt-5">
                            <h3 class="text-lg font-bold">Entrenar con el detector de postura</h3>
                            <p class="mt-2 leading-relaxed text-gray-600 dark:text-gray-300">
                                Activa la cámara y haz sentadillas, curl de bíceps o flexiones. Solo cuenta las repeticiones completas y puntúa tu técnica.
                            </p>
                        </div>

                        <div class="border-t-2 border-blue-600 pt-5">
                            <h3 class="text-lg font-bold">Seguir tu progreso en Mi Panel</h3>
                            <p class="mt-2 leading-relaxed text-gray-600 dark:text-gray-300">
                                Ve tus repeticiones por semana, la evolución de tu técnica y tu meta semanal. También calcula tu IMC, tu gasto calórico diario y una distribución base de macronutrientes.
                            </p>
                        </div>

                        <div class="border-t-2 border-blue-600 pt-5">
                            <h3 class="text-lg font-bold">Preguntar al asistente IA</h3>
                            <p class="mt-2 leading-relaxed text-gray-600 dark:text-gray-300">
                                Consulta dudas de entrenamiento o alimentación. El asistente responde con base en tu perfil físico y tus últimas sesiones.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Cómo empiezas -->
            <section class="mx-auto grid max-w-6xl gap-10 px-6 py-16 lg:grid-cols-3 lg:py-20">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Cómo empezar</h2>
                    <p class="mt-3 max-w-sm leading-relaxed text-gray-600 dark:text-gray-300">
                        Necesitas un dispositivo con cámara y un espacio donde te vean el cuerpo completo.
                    </p>
                </div>

                <ol class="space-y-8 border-l-2 border-gray-200 pl-6 dark:border-gray-700 lg:col-span-2">
                    <li>
                        <div class="text-sm font-semibold text-blue-600 dark:text-blue-400">Paso 1</div>
                        <h3 class="mt-1 text-lg font-bold">Crea tu cuenta y tu perfil físico</h3>
                        <p class="mt-1 max-w-xl leading-relaxed text-gray-600 dark:text-gray-300">Indica tu peso, estatura, fecha de nacimiento y nivel de actividad.</p>
                    </li>
                    <li>
                        <div class="text-sm font-semibold text-blue-600 dark:text-blue-400">Paso 2</div>
                        <h3 class="mt-1 text-lg font-bold">Entrena frente a la cámara</h3>
                        <p class="mt-1 max-w-xl leading-relaxed text-gray-600 dark:text-gray-300">Elige el ejercicio, colócate a una distancia donde la cámara te vea completo y empieza. Tus sesiones quedan guardadas.</p>
                    </li>
                    <li>
                        <div class="text-sm font-semibold text-blue-600 dark:text-blue-400">Paso 3</div>
                        <h3 class="mt-1 text-lg font-bold">Revisa tu progreso</h3>
                        <p class="mt-1 max-w-xl leading-relaxed text-gray-600 dark:text-gray-300">Consulta Mi Panel y pregunta al asistente cómo mejorar.</p>
                    </li>
                </ol>
            </section>

            <!-- Llamado final -->
            <section class="border-t border-gray-200 dark:border-gray-800">
                <div class="mx-auto flex max-w-6xl flex-col items-start justify-between gap-6 px-6 py-12 sm:flex-row sm:items-center">
                    <h2 class="text-xl font-bold tracking-tight sm:text-2xl">Haz tu primera sesión hoy</h2>
                    @auth
                        <a href="{{ route('entrenamiento.detector') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">
                            Empezar a entrenar
                        </a>
                    @else
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">
                                Crear mi cuenta
                            </a>
                        @endif
                    @endauth
                </div>
            </section>
        </main>

        <!-- Pie de página -->
        <footer class="border-t border-gray-200 dark:border-gray-800">
            <div class="mx-auto max-w-6xl px-6 py-6 text-xs leading-relaxed text-gray-500 dark:text-gray-400">
                <p>&copy; {{ date('Y') }} AppFitness. Proyecto de Ingeniería Informática.</p>
                <p class="mt-1">Las estimaciones y respuestas son orientativas y no reemplazan la opinión de un profesional de la salud.</p>
                <p class="mt-2">
                    <a href="{{ route('privacidad') }}" class="underline hover:text-gray-700 dark:hover:text-gray-200">Política de privacidad</a>
                    &middot;
                    <a href="{{ route('privacidad') }}#aviso-medico" class="underline hover:text-gray-700 dark:hover:text-gray-200">Aviso médico</a>
                </p>
            </div>
        </footer>

        <!-- Cambio de modo claro / oscuro -->
        <script>
            function toggleDarkMode() {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            }
        </script>
    </body>
</html>
