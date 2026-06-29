<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requerimiento_cliente_tareas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requerimiento_cliente_id');
            $table->string('nombre');
            $table->boolean('completada')->default(false);
            $table->timestamps();

            $table->foreign('requerimiento_cliente_id')
                  ->references('id')
                  ->on('requerimiento_cliente')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requerimiento_cliente_tareas');
    }
};
