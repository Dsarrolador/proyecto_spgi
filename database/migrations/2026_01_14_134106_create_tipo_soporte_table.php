<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            // Si ya existe el campo, no lo vuelve a crear (por si hiciste pruebas)
            if (!Schema::hasColumn('requerimiento_cliente', 'tipo_soporte_id')) {
                $table->unsignedBigInteger('tipo_soporte_id')->nullable()->after('estado');

                $table->foreign('tipo_soporte_id')
                      ->references('id')
                      ->on('tipo_soporte')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            if (Schema::hasColumn('requerimiento_cliente', 'tipo_soporte_id')) {
                $table->dropForeign(['tipo_soporte_id']);
                $table->dropColumn('tipo_soporte_id');
            }
        });
    }
};
