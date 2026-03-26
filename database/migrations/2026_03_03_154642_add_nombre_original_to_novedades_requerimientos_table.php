<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNombreOriginalToNovedadesRequerimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('novedades_requerimientos', function (Blueprint $table) {
            $table->string('nombre_original')->nullable()->after('adjunto');
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
            $table->dropColumn('nombre_original');
        });
    }
}
