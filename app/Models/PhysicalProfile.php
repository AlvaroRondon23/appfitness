<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicalProfile extends Model
{
    // Le decimos a Laravel qué columnas tiene permitido rellenar
    protected $fillable = [
        'user_id',
        'weight',
        'height',
        'birth_date',
        'gender',
        'activity_level',
        'medical_conditions'
    ];

    // Relación: Un perfil físico le pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}