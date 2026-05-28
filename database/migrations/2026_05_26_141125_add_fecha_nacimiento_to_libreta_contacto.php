<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaNacimientoToLibretaContacto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('libreta_contacto', function (Blueprint $table) {
            $table->date('fecha_nacimiento')->nullable()->after('correo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('libreta_contacto', function (Blueprint $table) {
            $table->dropColumn('fecha_nacimiento');
        });
    }
}
