<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 'user' (por defecto) o 'admin'. Las cuentas existentes quedan como 'user'.
            $table->string('role', 20)->default('user')->index();
            // Si tiene fecha, la cuenta está desactivada y no puede usar la app.
            $table->timestamp('disabled_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'disabled_at']);
        });
    }
};
