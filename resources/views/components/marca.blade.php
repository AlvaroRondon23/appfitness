{{-- Marca de AppFitness (logo + nombre). Uso: <x-marca /> --}}
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
    </span>
    <span class="text-lg font-extrabold tracking-tight text-gray-900 dark:text-white">AppFitness</span>
</span>
