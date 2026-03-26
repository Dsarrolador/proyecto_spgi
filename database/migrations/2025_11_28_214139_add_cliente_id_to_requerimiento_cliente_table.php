<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClienteIdToRequerimientoClienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('requerimiento_cliente', function (Blueprint $table) {
        // Agregar campo cliente_id
        $table->unsignedBigInteger('cliente_id')->nullable()->after('id');

        // Agregar relación FK
        $table->foreign('cliente_id')
              ->references('id')->on('cliente_maestro')
              ->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('requerimiento_cliente', function (Blueprint $table) {
        $table->dropForeign(['cliente_id']);
        $table->dropColumn('cliente_id');
    });
}

}
