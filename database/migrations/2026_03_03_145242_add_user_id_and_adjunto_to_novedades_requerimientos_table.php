<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdAndAdjuntoToNovedadesRequerimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('novedades_requerimientos', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('cliente_id');
            $table->string('adjunto')->nullable()->after('novedad');

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('novedades_requerimientos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'adjunto']);
        });
    }
}
