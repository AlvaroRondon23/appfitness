<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ejercicios', function (Blueprint $table) {
            $table->text('descripcion')->nullable();
            // Si es false, el ejercicio no aparece en el detector ni acepta sesiones nuevas
            $table->boolean('activo')->default(true);
        });

        // Por si la tabla se creó sin marcas de tiempo (el modelo las actualiza al editar)
        foreach (['created_at', 'updated_at'] as $columna) {
            if (! Schema::hasColumn('ejercicios', $columna)) {
                Schema::table('ejercicios', fn (Blueprint $table) => $table->timestamp($columna)->nullable());
            }
        }
    }

    public function down(): void
    {
        Schema::table('ejercicios', function (Blueprint $table) {
            $table->dropColumn(['descripcion', 'activo']);
        });
    }
};
