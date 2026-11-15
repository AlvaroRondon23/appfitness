<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Mi cuenta
        </h2>
    </x-slot>

    <div class="py-12">
        {{-- En modo oscuro, los campos y etiquetas de esta página se adaptan a las tarjetas oscuras.
             Se hace aquí y no en los componentes porque login y registro mantienen la tarjeta clara. --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6
                    dark:[&_input]:bg-gray-900 dark:[&_input]:text-gray-100 dark:[&_input]:border-gray-600 dark:[&_input]:placeholder-gray-500
                    dark:[&_label]:text-gray-300">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg dark:ring-1 dark:ring-gray-700/50">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg dark:ring-1 dark:ring-gray-700/50">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg dark:ring-1 dark:ring-gray-700/50">
                <div class="max-w-xl">
                    @include('profile.partials.export-data')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg dark:ring-1 dark:ring-gray-700/50">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>