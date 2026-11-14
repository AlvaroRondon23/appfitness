<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PhysicalProfileController;
use App\Http\Middleware\CheckPhysicalProfile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\SesionEntrenamientoController;
use App\Models\SesionEntrenamiento;
use App\Services\EstadisticasEntrenamiento;
use App\Services\PerfilCalculos;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    
    // SUB-GRUPO: Rutas sujetas al filtro del Perfil Físico
    Route::middleware(CheckPhysicalProfile::class)->group(function () {
        
        Route::get('/dashboard', function () {
            $perfil = auth()->user()->physicalProfile;
            
            // Las 10 sesiones más recientes del usuario (se ordena por id: la última guardada va primero)
            $sesiones = SesionEntrenamiento::where('user_id', auth()->id())
                            ->orderByDesc('id')
                            ->take(10)
                            ->get();

            // Cálculos del perfil (IMC, gasto energético, macros) y estadísticas de rendimiento
            $calculos = app(PerfilCalculos::class)->para($perfil);
            $stats = app(EstadisticasEntrenamiento::class)->resumen(auth()->user());

            return view('dashboard', compact('perfil', 'sesiones', 'calculos', 'stats'));
        })->middleware('verified')->name('dashboard');

        // Formulario para crear el perfil físico (antes /perfil-prueba)
        Route::get('/perfil-fisico/crear', function () {
            return view('create-profile');
        })->name('perfil.crear');

        // /entrenamiento ahora lleva directo al detector (se mantiene el nombre de ruta
        // 'entrenamiento' para que los enlaces existentes sigan funcionando)
        Route::redirect('/entrenamiento', '/entrenamiento/detector')->name('entrenamiento');

        // Ruta para el Detector de Postura con MediaPipe
        Route::get('/entrenamiento/detector', function () {
            return view('entrenamiento.detector');
        })->name('entrenamiento.detector');

        // Guardar la sesión de entrenamiento que genera el detector
        Route::post('/entrenamiento/sesiones', [SesionEntrenamientoController::class, 'store'])
            ->name('entrenamiento.sesiones.store');

        // RUTAS DEL MÓDULO DE IA
        Route::get('/asistente', [AiAssistantController::class, 'index'])->name('asistente');
        Route::post('/asistente/consultar', [AiAssistantController::class, 'generateResponse'])
            ->middleware('throttle:20,1') // máximo 20 consultas por minuto
            ->name('asistente.consultar');
        Route::delete('/asistente/historial', [AiAssistantController::class, 'limpiar'])->name('asistente.limpiar');
        
    });

    // Operaciones del CRUD (Rutas del Controlador)
    Route::post('/physical-profile', [PhysicalProfileController::class, 'store']);
    Route::get('/perfil-fisico/editar', [PhysicalProfileController::class, 'edit']); 
    Route::put('/physical-profile', [PhysicalProfileController::class, 'update']);  
    Route::delete('/physical-profile', [PhysicalProfileController::class, 'destroy']); 
    
    // Rutas base de Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';