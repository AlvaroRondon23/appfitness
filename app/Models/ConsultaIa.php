<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultaIa extends Model
{
    protected $table = 'consultas_ia';

    protected $fillable = ['user_id', 'pregunta', 'respuesta', 'modelo'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
