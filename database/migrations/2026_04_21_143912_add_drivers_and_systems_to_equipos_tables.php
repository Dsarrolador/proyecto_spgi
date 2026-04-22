<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDriversAndSystemsToEquiposTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cat_equipos', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_doc_id')->nullable()->after('driver_url')->comment('Driver universal del catálogo');
            $table->foreign('driver_doc_id')->references('id')->on('wiki_documents')->onDelete('set null');
        });

        Schema::table('cliente_equipos', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_id')->nullable()->after('wiki_document_id')->comment('Driver específico para este equipo');
            $table->unsignedBigInteger('extra_system_id')->nullable()->after('driver_id')->comment('Sistema o herramientas adicionales');

            $table->foreign('driver_id')->references('id')->on('wiki_documents')->onDelete('set null');
            $table->foreign('extra_system_id')->references('id')->on('wiki_documents')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('cat_equipos', function (Blueprint $table) {
            $table->dropForeign(['driver_doc_id']);
            $table->dropColumn('driver_doc_id');
        });

        Schema::table('cliente_equipos', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropForeign(['extra_system_id']);
            $table->dropColumn(['driver_id', 'extra_system_id']);
        });
    }
}
