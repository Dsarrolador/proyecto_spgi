<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNovedadesRequerimientosTable extends Migration
{
    public function up()
    {
        Schema::create('novedades_requerimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requerimiento_id');
            $table->unsignedBigInteger('cliente_id');
            $table->text('novedad');
            $table->timestamps();

            // FOREIGN KEYS CORRECTAS
            $table->foreign('requerimiento_id')
                  ->references('id')->on('requerimiento_cliente')
                  ->onDelete('cascade');

            $table->foreign('cliente_id')
                  ->references('id')->on('cliente_maestro')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('novedades_requerimientos');
    }
}
