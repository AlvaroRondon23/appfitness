<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ejercicio extends Model
{
    protected $table = 'ejercicios';

    protected $fillable = ['clave', 'nombre'];

    public function sesiones(): HasMany
    {
        // sesion_entrenamientos.ejercicio -> ejercicios.clave
        return $this->hasMany(SesionEntrenamiento::class, 'ejercicio', 'clave');
    }
}
