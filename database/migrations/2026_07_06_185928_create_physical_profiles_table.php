<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('physical_profiles', function (Blueprint $table) {
            $table->id();
            
            // Relación con la tabla de usuarios (si se borra el usuario, se borra su perfil)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Atributos físicos fundamentales
            $table->decimal('weight', 5, 2); // Peso en kg (permite valores como 120.50)
            $table->integer('height');       // Estatura en centímetros (ej: 178)
            $table->date('birth_date');      // Fecha de nacimiento para calcular la edad exacta
            $table->string('gender');        // Género (Masculino, Femenino, etc.)
            $table->string('activity_level');// Nivel de actividad (Sedentario, Moderado, Activo)
            
            // Campo de texto libre para lesiones o enfermedades (nullable significa opcional)
            $table->text('medical_conditions')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('physical_profiles');
    }
};