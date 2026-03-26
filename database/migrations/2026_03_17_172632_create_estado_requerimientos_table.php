<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstadoRequerimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estado_requerimientos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('color')->nullable();
            $table->timestamps();
        });

        // Seed initial states
        \DB::table('estado_requerimientos')->insert([
            ['nombre' => 'Pendiente', 'color' => 'bg-secondary', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'En progreso', 'color' => 'bg-warning text-dark', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Completado', 'color' => 'bg-success', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estado_requerimientos');
    }
}
