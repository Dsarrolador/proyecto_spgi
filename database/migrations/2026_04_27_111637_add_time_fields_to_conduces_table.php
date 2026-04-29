<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeFieldsToConducesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conduces', function (Blueprint $table) {
            $table->string('tipo')->default('trabajo')->after('id');
            $table->string('hora_entrada')->nullable()->after('observaciones');
            $table->string('hora_salida')->nullable()->after('hora_entrada');
            $table->string('cantidad_horas')->nullable()->after('hora_salida');
        });
    }

    public function down()
    {
        Schema::table('conduces', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'hora_entrada', 'hora_salida', 'cantidad_horas']);
        });
    }
}
