<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlToNotificacionesSistemaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notificaciones_sistema', function (Blueprint $table) {
            $table->string('url')->nullable()->after('mensaje');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notificaciones_sistema', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }
}
