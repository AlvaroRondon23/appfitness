<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registro técnico de una consulta al asistente (sin el contenido de la pregunta ni de la respuesta).
 */
class RegistroIa extends Model
{
    protected $table = 'registros_ia';

    public const RESULTADOS = [
        'ok'           => 'Correcta',
        'bloqueada'    => 'Bloqueada por seguridad',
        'sin_texto'    => 'Sin texto en la respuesta',
        'sin_conexion' => 'Sin conexión',
        'rechazada'    => 'Rechazada por el servicio',
        'sobrecarga'   => 'Servicio saturado o sin tiempo',
        'sin_clave'    => 'Sin clave configurada',
    ];

    protected $fillable = [
        'user_id', 'resultado', 'modelo', 'http_status',
        'duracion_ms', 'caracteres_pregunta', 'caracteres_respuesta',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
