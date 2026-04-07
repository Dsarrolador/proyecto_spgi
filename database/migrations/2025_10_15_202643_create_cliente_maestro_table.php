<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClienteMaestroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::create('cliente_maestro', function (Blueprint $table)  {
    $table->engine = 'InnoDB';
    $table->charset = 'utf8';
    $table->collation = 'utf8_general_ci';

    $table->id();
    $table->string('nombre', 150);
    $table->string('rnc', 20)->nullable();
    $table->string('clasificacion_negocio', 50)->nullable();
    $table->string('clasificacion_interna', 5)->nullable();
    $table->string('tipo_cliente', 50)->nullable();
    $table->text('direccion_escrita')->nullable();
    $table->string('coordenada_google_maps', 255)->nullable();
    $table->string('telefono_principal', 20)->nullable();
    $table->text('notas')->nullable();
    $table->timestamps();
});



}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cliente_maestro');
    }
}
