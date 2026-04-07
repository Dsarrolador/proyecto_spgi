<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFinalizacionFieldsToRequerimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {

            if (!Schema::hasColumn('requerimiento_cliente', 'fecha_finalizado')) {
                $table->dateTime('fecha_finalizado')
                      ->nullable()
                      ->after('created_at');
            }

            if (!Schema::hasColumn('requerimiento_cliente', 'tiempo_invertido')) {
                $table->string('tiempo_invertido', 20)
                      ->nullable()
                      ->after('fecha_finalizado');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {

            if (Schema::hasColumn('requerimiento_cliente', 'tiempo_invertido')) {
                $table->dropColumn('tiempo_invertido');
            }

            if (Schema::hasColumn('requerimiento_cliente', 'fecha_finalizado')) {
                $table->dropColumn('fecha_finalizado');
            }
        });
    }
}
