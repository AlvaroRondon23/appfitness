<section class="space-y-4">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Tus datos
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Descarga una copia en formato JSON de lo que AppFitness guarda de ti: datos de la cuenta, perfil físico (con tus condiciones médicas), sesiones de entrenamiento y consultas al asistente de IA. No incluye tu contraseña.
        </p>
    </header>

    <div class="flex flex-wrap items-center gap-4">
        <a href="{{ route('profile.exportar') }}"
           class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800">
            Descargar mis datos
        </a>

        <a href="{{ route('privacidad') }}" class="text-sm text-gray-600 underline hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
            Política de privacidad
        </a>
    </div>
</section>
