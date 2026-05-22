<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTarifariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tarifarios', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->string('basico_int')->nullable();
            $table->string('avanzado_int')->nullable();
            $table->string('basico_ext')->nullable();
            $table->string('avanzado_ext')->nullable();
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
        Schema::dropIfExists('tarifarios');
    }
}
