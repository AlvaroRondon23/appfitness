<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'privacy_accepted_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'privacy_accepted_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación: Un usuario tiene un perfil físico
    public function physicalProfile()
    {
        return $this->hasOne(PhysicalProfile::class);
    }

    // Relación: Un usuario tiene muchas sesiones de entrenamiento
    public function sesiones()
    {
        return $this->hasMany(SesionEntrenamiento::class);
    }

    // Relación: Un usuario tiene muchas consultas guardadas al asistente de IA
    public function consultasIa()
    {
        return $this->hasMany(ConsultaIa::class);
    }
}
