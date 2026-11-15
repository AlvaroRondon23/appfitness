<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Eliminar cuenta
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Al eliminar tu cuenta se borran de forma permanente tu perfil físico (con tus condiciones médicas), tus sesiones de entrenamiento y tus consultas guardadas al asistente de IA. Antes de continuar, puedes descargar una copia en la sección "Tus datos".
        </p>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Lo que ya se envió a Gemini para generar respuestas queda fuera del control de AppFitness. Consulta la <a href="{{ route('privacidad') }}#asistente-ia" class="underline hover:text-gray-900 dark:hover:text-gray-200">política de privacidad</a>.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Eliminar cuenta</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                ¿Seguro que quieres eliminar tu cuenta?
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Esta acción no se puede deshacer: se borrarán tu perfil, tus sesiones y tus consultas al asistente. Escribe tu contraseña para confirmar.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Contraseña" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Contraseña"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Cancelar
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    Eliminar cuenta
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
