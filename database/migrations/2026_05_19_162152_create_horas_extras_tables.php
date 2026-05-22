<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorasExtrasTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horas_extras', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->date('fecha_registro');
            $table->string('estado')->default('Borrador');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('responsable_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('horas_extras_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hora_extra_id')->constrained('horas_extras')->onDelete('cascade');
            $table->date('fecha');
            $table->string('colaborador');
            $table->string('concepto');
            $table->time('hora_inicio');
            $table->time('hora_salida');
            $table->decimal('total_horas', 5, 2);
            $table->decimal('tarifa_hora', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('horas_extras_detalles');
        Schema::dropIfExists('horas_extras');
    }
}
