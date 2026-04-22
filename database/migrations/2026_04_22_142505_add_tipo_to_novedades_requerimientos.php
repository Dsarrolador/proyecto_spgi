<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoToNovedadesRequerimientos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('novedades_requerimientos', function (Blueprint $table) {
            $table->string('tipo', 20)->default('cliente')->after('novedad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('novedades_requerimientos', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
}
