<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catálogo de ejercicios: una sola fuente de verdad para los nombres válidos
        Schema::create('ejercicios', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique(); // 'sentadilla', 'biceps', 'flexion'
            $table->string('nombre');          // Texto para mostrar al usuario
            $table->timestamps();
        });

        $base = [
            'sentadilla' => 'Sentadillas',
            'biceps'     => 'Curl de Bíceps',
            'flexion'    => 'Flexiones',
        ];

        // Si ya existían sesiones con otros valores, se agregan al catálogo para no perder datos
        foreach (DB::table('sesion_entrenamientos')->distinct()->pluck('ejercicio') as $clave) {
            if (!isset($base[$clave])) {
                $base[$clave] = ucfirst($clave);
            }
        }

        $ahora = now();
        foreach ($base as $clave => $nombre) {
            DB::table('ejercicios')->insert([
                'clave'      => $clave,
                'nombre'     => $nombre,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }

        Schema::table('sesion_entrenamientos', function (Blueprint $table) {
            // La columna 'ejercicio' pasa a ser clave foránea del catálogo (el código no cambia)
            $table->foreign('ejercicio')->references('clave')->on('ejercicios')
                  ->cascadeOnUpdate()->restrictOnDelete();

            // Acelera el historial y las estadísticas por usuario y fecha
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('sesion_entrenamientos', function (Blueprint $table) {
            $table->dropForeign(['ejercicio']);
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::dropIfExists('ejercicios');
    }
};
