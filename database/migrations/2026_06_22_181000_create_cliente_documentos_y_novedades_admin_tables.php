<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClienteDocumentosYNovedadesAdminTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Documentos de administración del cliente (Contratos y documentación)
        Schema::create('cliente_documentos_admin', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->string('nombre');
            $table->string('archivo_path');
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('cliente_maestro')->onDelete('cascade');
        });

        // Novedades o contactos de administración del cliente
        Schema::create('cliente_novedades_admin', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('fecha');
            $table->string('medio'); // Llamada, Correo, WhatsApp, Reunión, etc.
            $table->text('detalle');
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('cliente_maestro')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cliente_novedades_admin');
        Schema::dropIfExists('cliente_documentos_admin');
    }
}
