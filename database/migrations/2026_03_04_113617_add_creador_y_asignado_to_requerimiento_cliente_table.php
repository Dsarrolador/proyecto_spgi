<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            // 1) Crear columnas si no existen
            if (!Schema::hasColumn('requerimiento_cliente', 'creador_user_id')) {
                $table->unsignedBigInteger('creador_user_id')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('requerimiento_cliente', 'asignado_user_id')) {
                $table->unsignedBigInteger('asignado_user_id')->nullable()->after('creador_user_id');
            }

            /**
             * 2) Asegurar FKs sin adivinar nombres:
             * En tu BD los nombres reales son:
             * - requerimiento_cliente_creador_user_id_foreign
             * - requerimiento_cliente_asignado_user_id_foreign
             */

            // 3) Crear FKs con nombres explícitos para EVITAR conflictos (Error 121)
            try {
                $table->foreign('creador_user_id', 'fk_req_cli_creador_v2')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            } catch (\Throwable $e) {}

            try {
                $table->foreign('asignado_user_id', 'fk_req_cli_asignado_v2')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            // Drop FKs si existen
            try { $table->dropForeign('requerimiento_cliente_asignado_user_id_foreign'); } catch (\Throwable $e) {}
            try { $table->dropForeign('requerimiento_cliente_creador_user_id_foreign'); } catch (\Throwable $e) {}

            // Drop columnas si existen
            if (Schema::hasColumn('requerimiento_cliente', 'asignado_user_id')) {
                $table->dropColumn('asignado_user_id');
            }

            if (Schema::hasColumn('requerimiento_cliente', 'creador_user_id')) {
                $table->dropColumn('creador_user_id');
            }
        });
    }
};