<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDriverNombreToClienteEquiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cliente_equipos', function (Blueprint $table) {
            $table->string('driver_nombre')->nullable()->after('driver_id')->comment('Nombre descriptivo del driver o manual');
        });
    }

    public function down()
    {
        Schema::table('cliente_equipos', function (Blueprint $table) {
            $table->dropColumn('driver_nombre');
        });
    }
}
