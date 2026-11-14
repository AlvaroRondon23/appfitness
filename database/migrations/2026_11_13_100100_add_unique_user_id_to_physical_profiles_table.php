<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Si algún usuario tiene varios perfiles, se conserva solo el más reciente
        DB::statement(
            'DELETE p1 FROM physical_profiles p1
             INNER JOIN physical_profiles p2
                ON p1.user_id = p2.user_id AND p1.id < p2.id'
        );

        // Garantiza la relación 1:1 entre users y physical_profiles
        Schema::table('physical_profiles', function (Blueprint $table) {
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('physical_profiles', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });
    }
};
