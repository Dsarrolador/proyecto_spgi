<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNovedadesProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('novedades_proyectos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requerimiento_proyecto_id');
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->text('novedad');
            $table->string('tipo'); // 'interno' o 'cliente'
            $table->string('adjunto')->nullable();
            $table->string('nombre_original')->nullable();
            $table->timestamps();

            $table->foreign('requerimiento_proyecto_id')
                  ->references('id')->on('requerimiento_proyecto')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('novedades_proyectos');
    }
}
