<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// 'role' y 'disabled_at' NO están en Fillable a propósito: nadie puede volverse admin
// ni reactivarse enviando un formulario. Solo se asignan con forceFill() desde código de confianza.
#[Fillable(['name', 'email', 'password', 'privacy_accepted_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROL_USER = 'user';
    public const ROL_ADMIN = 'admin';
    public const ROLES = [self::ROL_USER, self::ROL_ADMIN];

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
            'disabled_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function esAdmin(): bool
    {
        return $this->role === self::ROL_ADMIN;
    }

    public function estaDesactivada(): bool
    {
        return $this->disabled_at !== null;
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

    // Relación: registro técnico (sin contenido) de las consultas al asistente
    public function registrosIa()
    {
        return $this->hasMany(RegistroIa::class);
    }
}
