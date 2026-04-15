<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaInicioToRequerimientoCliente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            $table->timestamp('fecha_inicio_recurrencia')->nullable()->after('frecuencia');
        });
    }

    public function down()
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            $table->dropColumn('fecha_inicio_recurrencia');
        });
    }
}
