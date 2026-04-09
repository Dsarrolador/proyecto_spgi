<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecurrenceToRequerimientoCliente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            $table->boolean('es_recurrente')->default(false)->after('estado_id');
            $table->string('frecuencia')->nullable()->after('es_recurrente');
            $table->timestamp('proxima_fecha_ejecucion')->nullable()->after('frecuencia');
        });
    }

    public function down()
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            $table->dropColumn(['es_recurrente', 'frecuencia', 'proxima_fecha_ejecucion']);
        });
    }
}
