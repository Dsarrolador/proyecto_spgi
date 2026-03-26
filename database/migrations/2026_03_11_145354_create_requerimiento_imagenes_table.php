<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequerimientoImagenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requerimiento_imagenes', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('requerimiento_id');

    $table->string('imagen');

    $table->timestamps();

    $table->foreign('requerimiento_id')
        ->references('id')
        ->on('requerimiento_cliente')
        ->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requerimiento_imagenes');
    }
}
