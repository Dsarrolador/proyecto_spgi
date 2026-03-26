<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequerimientoProyectoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('requerimiento_proyecto', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('id_proyecto');

        $table->unsignedBigInteger('cliente_id')->nullable();
        $table->unsignedBigInteger('contacto_id')->nullable();
        $table->unsignedBigInteger('tipo_soporte_id')->nullable();

        $table->text('texto_imagen');
        $table->string('foto')->nullable();

        $table->string('tiempo_transcurrido')->nullable();
        $table->string('estado')->default('Pendiente');

        $table->unsignedBigInteger('user_id')->nullable();

        $table->dateTime('fecha_finalizado')->nullable();
        $table->string('tiempo_invertido')->nullable();

        $table->boolean('facturado')->default(false);

        $table->timestamps();

        $table->foreign('id_proyecto')
              ->references('id')
              ->on('proyectos')
              ->onDelete('cascade');
    });
}
    public function down()
    {
        Schema::dropIfExists('requerimiento_proyecto');
    }
}
