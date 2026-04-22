<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJerarquiaYWikiToClienteEquiposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cliente_equipos', function (Blueprint $table) {
            $table->string('alias')->nullable()->after('cat_equipo_id')->comment('Nombre coloquial del equipo raíz');
            $table->unsignedBigInteger('parent_id')->nullable()->after('alias')->comment('Hace referencia a la misma tabla para periféricos');
            $table->unsignedBigInteger('wiki_document_id')->nullable()->after('parent_id')->comment('Referencia al documento de Sistema en la Wiki');

            $table->foreign('parent_id')->references('id')->on('cliente_equipos')->onDelete('cascade');
            $table->foreign('wiki_document_id')->references('id')->on('wiki_documents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cliente_equipos', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['wiki_document_id']);
            $table->dropColumn(['alias', 'parent_id', 'wiki_document_id']);
        });
    }
}
