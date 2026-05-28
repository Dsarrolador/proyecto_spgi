<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitasTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_visitado');
            $table->string('correo_visitado')->nullable();
            $table->string('nombre_recibio')->nullable();
            $table->string('telefono_recibio')->nullable();
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('total_puntos')->default(0);
            $table->string('estado_cliente')->nullable();
            $table->text('accion_sugerida')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('template_id')->references('id')->on('checklist_templates')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('visita_respuestas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visita_id');
            $table->unsignedBigInteger('question_id');
            $table->string('respuesta_seleccionada')->nullable();
            $table->integer('puntos')->default(0);
            $table->text('observaciones')->nullable();
            $table->text('recomendacion')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('visita_id')->references('id')->on('visitas')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('checklist_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visita_respuestas');
        Schema::dropIfExists('visitas');
    }
}
