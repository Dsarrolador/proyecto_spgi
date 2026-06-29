<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddComprobantePagoPathToEstadoCuentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('estado_cuentas', function (Blueprint $table) {
            $table->string('comprobante_pago_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estado_cuentas', function (Blueprint $table) {
            $table->dropColumn('comprobante_pago_path');
        });
    }
}
