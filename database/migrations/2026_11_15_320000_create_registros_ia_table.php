<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Registro técnico de cada consulta al asistente. NO guarda el contenido de la pregunta ni de la respuesta.
        Schema::create('registros_ia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('resultado', 30);
            $table->string('modelo', 100)->nullable();
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->unsignedInteger('duracion_ms')->default(0);
            $table->unsignedInteger('caracteres_pregunta')->default(0);
            $table->unsignedInteger('caracteres_respuesta')->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index('resultado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registros_ia');
    }
};
