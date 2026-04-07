<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        // 1) Agregar columna tipo_soporte_id SOLO si no existe
        if (!Schema::hasColumn('requerimiento_cliente', 'tipo_soporte_id')) {
            Schema::table('requerimiento_cliente', function (Blueprint $table) {
                $table->unsignedBigInteger('tipo_soporte_id')
                      ->nullable()
                      ->after('contacto_id');
            });
        }

        // 2) Agregar columna foto SOLO si no existe (tu INSERT la está enviando)
        if (!Schema::hasColumn('requerimiento_cliente', 'foto')) {
            Schema::table('requerimiento_cliente', function (Blueprint $table) {
                $table->string('foto')->nullable()->after('texto_imagen');
            });
        }

        // 3) Agregar timestamps SOLO si no existen (tu INSERT la está enviando)
        $hasCreated = Schema::hasColumn('requerimiento_cliente', 'created_at');
        $hasUpdated = Schema::hasColumn('requerimiento_cliente', 'updated_at');

        if (!$hasCreated && !$hasUpdated) {
            Schema::table('requerimiento_cliente', function (Blueprint $table) {
                $table->timestamps();
            });
        }

        // 4) Intentar crear FK (si ya existe o falla, no rompemos migrate)
        // Nota: Laravel no trae un "hasForeignKey" nativo fácil, por eso try/catch.
        try {
            Schema::table('requerimiento_cliente', function (Blueprint $table) {
                $table->foreign('tipo_soporte_id')
                      ->references('id')
                      ->on('tipo_soporte')
                      ->nullOnDelete();
            });
        } catch (\Throwable $e) {
            // Ignorar si ya existe o si el motor no soporta FKs
        }
    }

    public function down(): void
    {
        // Quitar FK si existe (si falla, ignoramos)
        try {
            Schema::table('requerimiento_cliente', function (Blueprint $table) {
                $table->dropForeign(['tipo_soporte_id']);
            });
        } catch (\Throwable $e) {
            // ignore
        }

        // Quitar columnas si existen
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            if (Schema::hasColumn('requerimiento_cliente', 'tipo_soporte_id')) {
                $table->dropColumn('tipo_soporte_id');
            }

            if (Schema::hasColumn('requerimiento_cliente', 'foto')) {
                $table->dropColumn('foto');
            }

            if (Schema::hasColumn('requerimiento_cliente', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('requerimiento_cliente', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
};
