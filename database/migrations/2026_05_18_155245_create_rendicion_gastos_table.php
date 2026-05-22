<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rendicion_gastos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rendicion_id');
            $table->date('fecha');
            $table->string('concepto');
            $table->string('proveedor');
            $table->decimal('monto', 10, 2);
            $table->string('metodo_pago'); // 'Efectivo', 'Tarjeta 1704', 'Reembolso'
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('rendicion_id')->references('id')->on('rendiciones')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rendicion_gastos');
    }
};
