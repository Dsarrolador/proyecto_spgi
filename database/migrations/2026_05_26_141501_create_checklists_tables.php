<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_templates', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('checklist_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->string('pregunta');
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('checklist_templates')->onDelete('cascade');
        });

        Schema::create('checklist_predefined_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->string('respuesta');
            $table->integer('puntos')->default(0);
            $table->string('observacion_default')->nullable();
            $table->string('recomendacion_default')->nullable();
            $table->timestamps();

            $table->foreign('question_id')->references('id')->on('checklist_questions')->onDelete('cascade');
        });

        Schema::create('lead_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('total_puntos')->default(0);
            $table->string('estado_cliente')->nullable(); // Estable, Critico, etc
            $table->text('accion_sugerida')->nullable();
            $table->timestamps();

            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('checklist_templates')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('lead_checklist_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_checklist_id');
            $table->unsignedBigInteger('question_id');
            $table->string('respuesta_seleccionada')->nullable();
            $table->integer('puntos')->default(0);
            $table->text('observaciones')->nullable();
            $table->text('recomendacion')->nullable();
            $table->timestamps();

            $table->foreign('lead_checklist_id')->references('id')->on('lead_checklists')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('checklist_questions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lead_checklist_answers');
        Schema::dropIfExists('lead_checklists');
        Schema::dropIfExists('checklist_predefined_answers');
        Schema::dropIfExists('checklist_questions');
        Schema::dropIfExists('checklist_templates');
    }
}
