<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoEquipoIdToCatEquiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cat_equipos', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_equipo_id')->nullable()->after('nombre');
            $table->foreign('tipo_equipo_id')->references('id')->on('cat_tipos_equipo')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cat_equipos', function (Blueprint $table) {
            //
        });
    }
}
