<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatEquiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cat_equipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tipo'); // Monitor, Printer, Lectoras, etc.
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->text('caracteristicas')->nullable();
            $table->text('configuracion_estandar')->nullable();
            $table->string('driver_url')->nullable();
            $table->boolean('activo')->default(true);
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
        Schema::dropIfExists('cat_equipos');
    }
}
