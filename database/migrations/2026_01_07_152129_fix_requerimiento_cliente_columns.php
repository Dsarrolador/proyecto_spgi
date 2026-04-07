<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        // ✅ SOLO agregar lo que falta (NO tocar tipo_soporte_id)
        Schema::table('requerimiento_cliente', function (Blueprint $table) {

            // foto
            if (!Schema::hasColumn('requerimiento_cliente', 'foto')) {
                $table->string('foto')->nullable()->after('texto_imagen');
            }

            // timestamps
            $hasCreated = Schema::hasColumn('requerimiento_cliente', 'created_at');
            $hasUpdated = Schema::hasColumn('requerimiento_cliente', 'updated_at');

            if (!$hasCreated && !$hasUpdated) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {

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
