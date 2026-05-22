<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValorAndTipoToTarifariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tarifarios', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_soporte_id')->nullable()->after('id');
            $table->string('valor')->nullable()->after('avanzado_ext');
            
            $table->foreign('tipo_soporte_id')->references('id')->on('tipo_soporte')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tarifarios', function (Blueprint $table) {
            $table->dropForeign(['tipo_soporte_id']);
            $table->dropColumn(['tipo_soporte_id', 'valor']);
        });
    }
}
