<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PhysicalProfileController;
use App\Http\Middleware\CheckPhysicalProfile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiAssistantController;
use App\Models\SesionEntrenamiento;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    
    // SUB-GRUPO: Rutas sujetas al filtro del Perfil Físico
    Route::middleware(CheckPhysicalProfile::class)->group(function () {
        
        Route::get('/dashboard', function () {
            $perfil = auth()->user()->physicalProfile;
            
            // Obtenemos las sesiones del usuario actual ordenadas de la más reciente
            $sesiones = SesionEntrenamiento::where('user_id', auth()->id())
                            ->latest()
                            ->take(10)
                            ->get();

            return view('dashboard', compact('perfil', 'sesiones'));
        })->middleware('verified')->name('dashboard');

        Route::get('/perfil-prueba', function () {
            return view('create-profile');
        });

        // Ruta para el Módulo Analizador de Entrenamiento
        Route::get('/entrenamiento', function () {
            return view('entrenamiento');
        })->name('entrenamiento');

        // Ruta para el Detector de Postura con MediaPipe
        Route::get('/entrenamiento/detector', function () {
            return view('entrenamiento.detector');
        })->name('entrenamiento.detector');

        // RUTAS DEL MÓDULO DE IA
        Route::get('/asistente', [AiAssistantController::class, 'index'])->name('asistente');
        Route::post('/asistente/consultar', [AiAssistantController::class, 'generateResponse'])->name('asistente.consultar');
        
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