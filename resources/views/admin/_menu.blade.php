@php
    $pestanas = [
        ['ruta' => 'admin.panel',          'activa' => 'admin.panel',      'texto' => 'Resumen'],
        ['ruta' => 'admin.usuarios.index', 'activa' => 'admin.usuarios.*', 'texto' => 'Usuarios'],
        ['ruta' => 'admin.ejercicios.index', 'activa' => 'admin.ejercicios.*', 'texto' => 'Ejercicios'],
        ['ruta' => 'admin.asistente.index', 'activa' => 'admin.asistente.*', 'texto' => 'Asistente IA'],
    ];
@endphp

<div class="flex flex-wrap gap-2" role="navigation" aria-label="Secciones de administración">
    @foreach ($pestanas as $p)
        @php $activa = request()->routeIs($p['activa']); @endphp
        <a href="{{ route($p['ruta']) }}" @if ($activa) aria-current="page" @endif
           class="rounded-md px-4 py-2 text-sm font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 {{ $activa ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            {{ $p['texto'] }}
        </a>
    @endforeach
</div>

@if (session('status'))
    <div class="rounded-md border border-green-300 bg-green-50 p-3 text-sm text-green-800 dark:border-green-700/60 dark:bg-green-900/20 dark:text-green-200" role="status">{{ session('status') }}</div>
@endif
@if (session('error'))
    <div class="rounded-md border border-red-300 bg-red-50 p-3 text-sm text-red-800 dark:border-red-700/60 dark:bg-red-900/20 dark:text-red-200" role="alert">{{ session('error') }}</div>
@endif
