<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SesionEntrenamiento extends Model
{
    // Laravel pluralizaría "SesionEntrenamiento" mal, así que indicamos la tabla
    protected $table = 'sesion_entrenamientos';

    protected $fillable = [
        'user_id',
        'ejercicio',
        'repeticiones',
        'tiempo_segundos',
    ];

    // Relación: una sesión de entrenamiento pertenece a un usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
