<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyEstadoInRequerimientosTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Add estado_id
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            $table->unsignedBigInteger('estado_id')->nullable()->after('estado');
        });

        Schema::table('requerimiento_proyecto', function (Blueprint $table) {
            $table->unsignedBigInteger('estado_id')->nullable()->after('estado');
        });

        // 2. Map existing strings
        $estados = \DB::table('estado_requerimientos')->get();
        foreach ($estados as $estado) {
            \DB::table('requerimiento_cliente')
               ->where('estado', $estado->nombre)
               ->update(['estado_id' => $estado->id]);
               
            \DB::table('requerimiento_proyecto')
               ->where('estado', $estado->nombre)
               ->update(['estado_id' => $estado->id]);
        }

        // Default to Pendiente if null
        \DB::table('requerimiento_cliente')->whereNull('estado_id')->update(['estado_id' => 1]);
        \DB::table('requerimiento_proyecto')->whereNull('estado_id')->update(['estado_id' => 1]);

        // 3. Drop string column & add foreign key
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            $table->dropColumn('estado');
            $table->foreign('estado_id')->references('id')->on('estado_requerimientos');
        });

        Schema::table('requerimiento_proyecto', function (Blueprint $table) {
            $table->dropColumn('estado');
            $table->foreign('estado_id')->references('id')->on('estado_requerimientos');
        });
    }

    public function down()
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            $table->dropForeign(['estado_id']);
            $table->string('estado')->nullable();
        });

        Schema::table('requerimiento_proyecto', function (Blueprint $table) {
            $table->dropForeign(['estado_id']);
            $table->string('estado')->nullable();
        });

        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            $table->dropColumn('estado_id');
        });

        Schema::table('requerimiento_proyecto', function (Blueprint $table) {
            $table->dropColumn('estado_id');
        });
    }
}
