<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactoIdToRequerimientoClienteTable extends Migration
{
    public function up()
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {

            // ✅ Agregar contacto_id
            if (!Schema::hasColumn('requerimiento_cliente', 'contacto_id')) {
                $table->unsignedBigInteger('contacto_id')->nullable()->after('cliente_id');
            }

            // ✅ Agregar lo que falta
            if (!Schema::hasColumn('requerimiento_cliente', 'tipo_soporte_id')) {
                $table->unsignedBigInteger('tipo_soporte_id')->nullable()->after('contacto_id');
            }

            if (!Schema::hasColumn('requerimiento_cliente', 'foto')) {
                $table->string('foto')->nullable()->after('texto_imagen');
            }

            if (!Schema::hasColumn('requerimiento_cliente', 'created_at') &&
                !Schema::hasColumn('requerimiento_cliente', 'updated_at')) {
                $table->timestamps();
            }
        });
    }

    public function down()
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {

            // Quitar FK si existe (si falla, ignoramos)
            try { $table->dropForeign(['tipo_soporte_id']); } catch (\Throwable $e) {}

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
}
