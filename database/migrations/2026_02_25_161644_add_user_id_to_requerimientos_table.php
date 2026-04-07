<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            if (!Schema::hasColumn('requerimiento_cliente', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('tipo_soporte_id') // cambia esto si tu columna se llama diferente
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            if (Schema::hasColumn('requerimiento_cliente', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};