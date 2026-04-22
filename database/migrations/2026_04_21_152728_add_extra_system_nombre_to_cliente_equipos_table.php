<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraSystemNombreToClienteEquiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cliente_equipos', function (Blueprint $table) {
            $table->string('extra_system_nombre')->nullable()->after('extra_system_id')->comment('Nombre descriptivo de la herramienta o sistema extra');
        });
    }

    public function down()
    {
        Schema::table('cliente_equipos', function (Blueprint $table) {
            $table->dropColumn('extra_system_nombre');
        });
    }
}
