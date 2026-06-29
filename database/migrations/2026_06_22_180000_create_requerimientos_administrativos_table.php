<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequerimientosAdministrativosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requerimientos_administrativos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('prioridad')->default('Media'); // Baja, Media, Alta
            $table->string('estado')->default('Pendiente'); // Pendiente, En Proceso, Completado, Cancelado
            $table->unsignedBigInteger('user_id'); // Creador
            $table->unsignedBigInteger('asignado_user_id')->nullable(); // Asignado
            $table->date('fecha_limite')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('asignado_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requerimientos_administrativos');
    }
}
