<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotesToRequerimientoProyecto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requerimiento_proyecto', function (Blueprint $table) {
            $table->text('notas_internas')->nullable();
            $table->text('notas_clientes')->nullable();
            $table->unsignedBigInteger('notas_last_user_id')->nullable();
            $table->boolean('notas_seen')->default(true);

            $table->foreign('notas_last_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
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
            $table->dropForeign(['notas_last_user_id']);
            $table->dropColumn(['notas_internas', 'notas_clientes', 'notas_last_user_id', 'notas_seen']);
        });
    }
}
