<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConducesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conduces', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requerimiento_id')->nullable()->index();
            $table->unsignedBigInteger('cliente_id')->nullable()->index();
            $table->unsignedBigInteger('contacto_id')->nullable()->index();
            $table->date('fecha')->nullable();
            $table->text('trabajo_realizar')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('conduce_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conduce_id')->index();
            $table->decimal('cantidad', 10, 2)->default(0);
            $table->string('descripcion')->nullable();
            $table->string('num_cotizacion')->nullable();
            $table->boolean('facturar')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('conduce_items');
        Schema::dropIfExists('conduces');
    }
}
