<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrioridadToRequerimientoClienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            $table->integer('prioridad')->default(3)->after('estado_id')->comment('1 a 5, siendo 5 la más urgente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            $table->dropColumn('prioridad');
        });
    }
}
