<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Ejercicios nuevos del Bloque 11: clave => nombre para mostrar
    private array $nuevos = [
        'salto_tijera'       => 'Salto de tijera',
        'elevacion_rodillas' => 'Elevación de rodillas',
    ];

    public function up(): void
    {
        $ahora = now();

        foreach ($this->nuevos as $clave => $nombre) {
            // Repetible: si la clave ya existe no se vuelve a insertar
            if (DB::table('ejercicios')->where('clave', $clave)->exists()) {
                continue;
            }

            DB::table('ejercicios')->insert([
                'clave'      => $clave,
                'nombre'     => $nombre,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->nuevos) as $clave) {
            // Solo se quitan del catálogo si ninguna sesión los usa (la clave foránea lo impediría)
            $enUso = DB::table('sesion_entrenamientos')->where('ejercicio', $clave)->exists();

            if (!$enUso) {
                DB::table('ejercicios')->where('clave', $clave)->delete();
            }
        }
    }
};
