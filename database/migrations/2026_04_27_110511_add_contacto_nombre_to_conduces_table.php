<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactoNombreToConducesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conduces', function (Blueprint $table) {
            $table->string('contacto_nombre')->nullable()->after('contacto_id');
        });
    }

    public function down()
    {
        Schema::table('conduces', function (Blueprint $table) {
            $table->dropColumn('contacto_nombre');
        });
    }
}
