<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sesion_entrenamientos', function (Blueprint $table) {
            // Puntaje de técnica de 0 a 100. Nullable porque las sesiones anteriores no lo tienen
            $table->unsignedTinyInteger('puntaje')->nullable()->after('tiempo_segundos');
        });
    }

    public function down(): void
    {
        Schema::table('sesion_entrenamientos', function (Blueprint $table) {
            $table->dropColumn('puntaje');
        });
    }
};