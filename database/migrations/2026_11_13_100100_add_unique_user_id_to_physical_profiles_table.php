<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Si algún usuario tiene varios perfiles, se conserva solo el más reciente.
        // Se evita el DELETE ... JOIN porque es exclusivo de MySQL y rompe en SQLite (tests).
        $conservar = DB::table('physical_profiles')
            ->selectRaw('MAX(id) AS id')
            ->groupBy('user_id')
            ->pluck('id');

        DB::table('physical_profiles')->whereNotIn('id', $conservar)->delete();

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