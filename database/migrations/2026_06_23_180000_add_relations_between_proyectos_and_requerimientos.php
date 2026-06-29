<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationsBetweenProyectosAndRequerimientos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            $table->unsignedBigInteger('proyecto_id')->nullable()->after('cliente_id');
            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('set null');
        });

        Schema::table('requerimiento_proyecto', function (Blueprint $table) {
            $table->unsignedBigInteger('requerimiento_cliente_id')->nullable()->after('id_proyecto');
            $table->foreign('requerimiento_cliente_id')->references('id')->on('requerimiento_cliente')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requerimiento_proyecto', function (Blueprint $table) {
            $table->dropForeign(['requerimiento_cliente_id']);
            $table->dropColumn('requerimiento_cliente_id');
        });

        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            $table->dropForeign(['proyecto_id']);
            $table->dropColumn('proyecto_id');
        });
    }
}
