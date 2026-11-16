<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Política de privacidad y aviso médico | AppFitness</title>

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

        <header class="mx-auto flex max-w-3xl items-center justify-between px-6 py-5">
            <a href="/" class="rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500" aria-label="AppFitness, inicio">
                <x-marca />
            </a>

            <button type="button" onclick="toggleDarkMode()" title="Cambiar entre modo claro y oscuro" aria-label="Cambiar entre modo claro y oscuro"
                    class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200">
                <svg class="hidden h-5 w-5 dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                </svg>
                <svg class="block h-5 w-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                </svg>
            </button>
        </header>

        <main class="mx-auto max-w-3xl px-6 pb-16 pt-6">
            <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Política de privacidad y aviso médico</h1>
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Última actualización: 15 de noviembre de 2026</p>

            <nav class="mt-8 rounded-lg border border-gray-200 p-4 text-sm dark:border-gray-700" aria-label="Contenido de esta página">
                <ul class="grid gap-1 sm:grid-cols-2">
                    <li><a class="text-blue-600 underline dark:text-blue-400" href="#quienes-somos">Quiénes somos</a></li>
                    <li><a class="text-blue-600 underline dark:text-blue-400" href="#datos">Qué datos guardamos</a></li>
                    <li><a class="text-blue-600 underline dark:text-blue-400" href="#camara">Tu cámara y el video</a></li>
                    <li><a class="text-blue-600 underline dark:text-blue-400" href="#asistente-ia">Asistente de IA (Gemini)</a></li>
                    <li><a class="text-blue-600 underline dark:text-blue-400" href="#uso">Para qué usamos tus datos</a></li>
                    <li><a class="text-blue-600 underline dark:text-blue-400" href="#conservacion">Conservación y borrado</a></li>
                    <li><a class="text-blue-600 underline dark:text-blue-400" href="#derechos">Tus derechos</a></li>
                    <li><a class="text-blue-600 underline dark:text-blue-400" href="#administracion">Administración de la aplicación</a></li>
                    <li><a class="text-blue-600 underline dark:text-blue-400" href="#cookies">Cookies y almacenamiento local</a></li>
                    <li><a class="text-blue-600 underline dark:text-blue-400" href="#seguridad">Seguridad</a></li>
                    <li><a class="text-blue-600 underline dark:text-blue-400" href="#aviso-medico">Aviso médico</a></li>
                    <li><a class="text-blue-600 underline dark:text-blue-400" href="#cambios">Cambios y contacto</a></li>
                </ul>
            </nav>

            <div class="mt-10 space-y-10 leading-relaxed text-gray-700 dark:text-gray-300">

                <section id="quienes-somos" class="scroll-mt-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Quiénes somos</h2>
                    <p class="mt-3">AppFitness es un proyecto académico de Ingeniería Informática que cuenta repeticiones y puntúa la técnica de ejercicios con la cámara del dispositivo. No es un servicio comercial ni un dispositivo médico. Esta página explica qué datos tratamos, para qué y cómo puedes controlarlos.</p>
                </section>

                <section id="datos" class="scroll-mt-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Qué datos guardamos</h2>
                    <ul class="mt-3 list-disc space-y-2 pl-6">
                        <li><strong>Cuenta:</strong> nombre, correo electrónico y contraseña (la contraseña se guarda cifrada, nunca en texto legible), y la fecha en que aceptaste esta política.</li>
                        <li><strong>Perfil físico:</strong> peso, estatura, fecha de nacimiento, género, nivel de actividad y, si decides escribirlas, tus condiciones médicas o lesiones. Este último campo es opcional y puede contener información de salud, que es especialmente sensible: escribe solo lo necesario.</li>
                        <li><strong>Sesiones de entrenamiento:</strong> ejercicio, repeticiones, duración, puntaje de técnica (de 0 a 100) y fecha.</li>
                        <li><strong>Consultas al asistente de IA:</strong> las preguntas que escribes y las respuestas recibidas, junto con el modelo que respondió.</li>
                        <li><strong>Registro técnico del asistente:</strong> por cada consulta, la fecha, si funcionó o falló, el modelo, la duración y el número de caracteres de la pregunta y de la respuesta. Nunca incluye el contenido.</li>
                    </ul>
                </section>

                <section id="camara" class="scroll-mt-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Tu cámara y el video</h2>
                    <p class="mt-3">El detector de postura funciona dentro de tu navegador. El video de tu cámara se procesa en tu dispositivo y no se envía ni se guarda en ningún servidor. Del entrenamiento solo se guarda el resumen numérico de la sesión (ejercicio, repeticiones, duración y puntaje).</p>
                </section>

                <section id="asistente-ia" class="scroll-mt-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Asistente de IA (Gemini)</h2>
                    <p class="mt-3">Cuando haces una pregunta al asistente, AppFitness la envía a Gemini, el servicio de inteligencia artificial de Google, junto con contexto para que la respuesta sea útil:</p>
                    <ul class="mt-3 list-disc space-y-2 pl-6">
                        <li>tu perfil físico (peso, estatura, edad, género y nivel de actividad) y las medidas calculadas a partir de él;</li>
                        <li>las condiciones médicas que hayas escrito en tu perfil;</li>
                        <li>un resumen de tus sesiones de entrenamiento;</li>
                        <li>tus últimas consultas de la conversación, para que el asistente recuerde el hilo.</li>
                    </ul>
                    <p class="mt-3">Estos datos salen de AppFitness hacia Google y se tratan según los términos y la política de privacidad de Google. Si eliminas tu cuenta o tu historial en AppFitness, no podemos borrar lo que ya se envió a Gemini. Si no quieres que se comparta cierta información, no la escribas en tu perfil ni en tus preguntas, o no uses el asistente. El resto de la app funciona sin él.</p>
                    <p class="mt-3">Al registrarte aceptas este envío cuando uses el asistente.</p>
                </section>

                <section id="uso" class="scroll-mt-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Para qué usamos tus datos</h2>
                    <ul class="mt-3 list-disc space-y-2 pl-6">
                        <li>Calcular tu IMC, tu gasto calórico y una distribución base de macronutrientes.</li>
                        <li>Mostrar tu progreso en Mi Panel.</li>
                        <li>Generar las respuestas del asistente de IA.</li>
                        <li>Mantener tu cuenta y su seguridad.</li>
                    </ul>
                    <p class="mt-3">No vendemos tus datos ni los usamos para publicidad.</p>
                </section>

                <section id="conservacion" class="scroll-mt-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Conservación y borrado</h2>
                    <p class="mt-3">Conservamos tus datos mientras tengas la cuenta. Puedes borrar la conversación con el asistente desde su pantalla y eliminar tu perfil físico desde Mi Panel. Si eliminas tu cuenta en <em>Mi cuenta</em>, se borran de forma permanente tu perfil físico, tus sesiones de entrenamiento, tus consultas al asistente y su registro técnico, además de tus sesiones abiertas. Si solo borras la conversación con el asistente, el registro técnico (sin contenido) se conserva hasta que elimines la cuenta.</p>
                </section>

                <section id="derechos" class="scroll-mt-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Tus derechos</h2>
                    <ul class="mt-3 list-disc space-y-2 pl-6">
                        <li><strong>Acceso y copia:</strong> en <em>Mi cuenta</em> puedes descargar tus datos en formato JSON.</li>
                        <li><strong>Rectificación:</strong> puedes editar tu cuenta y tu perfil físico en cualquier momento.</li>
                        <li><strong>Eliminación:</strong> puedes eliminar tu cuenta en <em>Mi cuenta</em>.</li>
                    </ul>
                    <p class="mt-3">Para cualquier otra solicitud, escribe al contacto indicado al final de esta página.</p>
                </section>

                <section id="administracion" class="scroll-mt-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Administración de la aplicación</h2>
                    <p class="mt-3">AppFitness tiene cuentas con rol de administrador, destinadas al equipo del proyecto para mantener la aplicación. Un administrador puede:</p>
                    <ul class="mt-3 list-disc space-y-2 pl-6">
                        <li>ver los datos de tu cuenta (nombre, correo, fecha de registro, rol y estado), si has completado tu perfil físico (solo sí o no) y el resumen de tus sesiones de entrenamiento (ejercicio, repeticiones, duración, puntaje y fecha);</li>
                        <li>ver estadísticas agregadas de toda la aplicación;</li>
                        <li>ver el registro técnico del asistente de IA (fecha, resultado, modelo, duración y número de caracteres, junto con tu nombre);</li>
                        <li>desactivar tu cuenta, cambiar su rol o eliminarla. La eliminación es permanente y borra los mismos datos que cuando la eliminas tú.</li>
                    </ul>
                    <p class="mt-3">Un administrador <strong>no</strong> puede ver tu contraseña (se guarda cifrada), los valores de tu perfil físico (peso, estatura, fecha de nacimiento, género, nivel de actividad ni condiciones médicas) ni el contenido de tus preguntas y respuestas con el asistente de IA.</p>
                </section>

                <section id="cookies" class="scroll-mt-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Cookies y almacenamiento local</h2>
                    <p class="mt-3">AppFitness usa solo lo necesario para funcionar: una cookie de sesión para mantenerte conectado y una cookie de seguridad contra ataques de falsificación de formularios. En tu navegador se guarda además tu preferencia de modo claro u oscuro. No usamos cookies de publicidad ni de seguimiento.</p>
                </section>

                <section id="seguridad" class="scroll-mt-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Seguridad</h2>
                    <p class="mt-3">Aplicamos medidas razonables para proteger tus datos: contraseñas cifradas, acceso solo con sesión iniciada y límites de intentos en el inicio de sesión y en el asistente. Ningún sistema es completamente seguro; al ser un proyecto académico, no podemos garantizar una protección absoluta.</p>
                </section>

                <section id="aviso-medico" class="scroll-mt-6 rounded-lg border border-amber-300 bg-amber-50 p-5 text-amber-950 dark:border-amber-700/60 dark:bg-amber-900/20 dark:text-amber-100">
                    <h2 class="text-xl font-bold">Aviso médico</h2>
                    <p class="mt-3">La información de AppFitness es orientativa. El IMC, el gasto calórico, los macronutrientes, el puntaje de técnica y las respuestas del asistente de IA son estimaciones y no reemplazan la opinión de un médico, nutricionista o entrenador.</p>
                    <ul class="mt-3 list-disc space-y-2 pl-6">
                        <li>Consulta a un profesional de la salud antes de empezar o cambiar un programa de ejercicio o de alimentación, sobre todo si tienes una lesión, una enfermedad o estás embarazada.</li>
                        <li>Las respuestas de la IA pueden contener errores. Contrástalas antes de aplicarlas.</li>
                        <li>Si sientes dolor, mareo o malestar mientras entrenas, detente. Ante una emergencia, llama a los servicios de emergencia de tu zona.</li>
                        <li>Entrenas bajo tu responsabilidad: elige un espacio seguro y despejado frente a la cámara.</li>
                    </ul>
                </section>

                <section id="cambios" class="scroll-mt-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Cambios y contacto</h2>
                    <p class="mt-3">Si esta política cambia, actualizaremos la fecha de esta página. Para consultas sobre privacidad o para ejercer tus derechos, escribe a <a class="text-blue-600 underline dark:text-blue-400" href="mailto:privacidad@appfitness.example">privacidad@appfitness.example</a>.</p>
                </section>
            </div>

            <p class="mt-12">
                <a href="/" class="text-sm font-semibold text-blue-600 underline hover:text-blue-700 dark:text-blue-400">Volver al inicio</a>
            </p>
        </main>

        <footer class="border-t border-gray-200 dark:border-gray-800">
            <div class="mx-auto max-w-3xl px-6 py-6 text-xs text-gray-500 dark:text-gray-400">
                <p>&copy; {{ date('Y') }} AppFitness. Proyecto de Ingeniería Informática.</p>
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
