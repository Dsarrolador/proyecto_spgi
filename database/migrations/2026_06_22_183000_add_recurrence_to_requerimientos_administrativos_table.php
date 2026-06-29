<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecurrenceToRequerimientosAdministrativosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requerimientos_administrativos', function (Blueprint $table) {
            $table->boolean('es_recurrente')->default(false)->after('estado');
            $table->string('frecuencia')->nullable()->after('es_recurrente');
            $table->date('fecha_inicio_recurrencia')->nullable()->after('frecuencia');
            $table->date('proxima_fecha_ejecucion')->nullable()->after('fecha_inicio_recurrencia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requerimientos_administrativos', function (Blueprint $table) {
            $table->dropColumn(['es_recurrente', 'frecuencia', 'fecha_inicio_recurrencia', 'proxima_fecha_ejecucion']);
        });
    }
}
