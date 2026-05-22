<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstadoCuentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estado_cuentas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_maestro_id')->nullable()->constrained('cliente_maestro')->nullOnDelete();
            $table->string('cliente_nombre');
            $table->string('factura_no');
            $table->string('nfc')->nullable();
            $table->date('fecha');
            $table->date('fecha_vencimiento');
            $table->string('producto');
            $table->decimal('balance', 12, 2);
            $table->string('moneda')->default('DOP');
            $table->decimal('tasa_cambio', 10, 2)->nullable();
            $table->date('fecha_pago')->nullable();
            $table->date('fecha_aplicado')->nullable();
            $table->string('recibo_no')->nullable();
            $table->decimal('total_pagado', 12, 2)->nullable();
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
        Schema::dropIfExists('estado_cuentas');
    }
}
