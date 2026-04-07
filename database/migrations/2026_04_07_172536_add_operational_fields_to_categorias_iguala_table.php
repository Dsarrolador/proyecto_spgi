<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOperationalFieldsToCategoriasIgualaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categorias_iguala', function (Blueprint $table) {
            $table->integer('cantidad_soporte_remoto')->default(0);
            $table->integer('cantidad_visitas')->default(0);
            $table->boolean('mantenimiento_sw_hw')->default(0);
            $table->boolean('equipo_prestamo')->default(0);
            $table->boolean('asistencia_vip')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categorias_iguala', function (Blueprint $table) {
            $table->dropColumn([
                'cantidad_soporte_remoto',
                'cantidad_visitas',
                'mantenimiento_sw_hw',
                'equipo_prestamo',
                'asistencia_vip'
            ]);
        });
    }
}
