<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClienteEntornoTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Documentos, IPs, Credenciales, Mapas, Reportes, Drivers
        Schema::create('cliente_entorno_documentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->string('tipo'); // IP, Mapa, Credencial, Reporte, Driver
            $table->string('nombre');
            $table->string('archivo_path')->nullable();
            $table->string('url')->nullable();
            $table->string('usuario')->nullable();
            $table->text('clave')->nullable(); // Guardaremos cifrada
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('cliente_maestro')->onDelete('cascade');
        });

        // 2. Listado de AnyDesk
        Schema::create('cliente_anydesks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->string('anydesk_id');
            $table->string('alias')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('cliente_maestro')->onDelete('cascade');
        });

        // 3. Bitácora de Notas de Sistema
        Schema::create('cliente_bitacoras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('user_id');
            $table->text('nota');
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('cliente_maestro')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 4. Inventario de Equipos (Relación con Catálogo)
        Schema::create('cliente_equipos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('cat_equipo_id');
            $table->string('serie')->nullable();
            $table->text('configuracion_especifica')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('cliente_maestro')->onDelete('cascade');
            $table->foreign('cat_equipo_id')->references('id')->on('cat_equipos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cliente_entorno_tables');
    }
}
