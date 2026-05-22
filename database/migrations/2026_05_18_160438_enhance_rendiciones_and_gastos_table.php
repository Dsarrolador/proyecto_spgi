<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Crear tabla metodos_pago
        if (!Schema::hasTable('metodos_pago')) {
            Schema::create('metodos_pago', function (Blueprint $table) {
                $table->id();
                $table->string('nombre')->unique();
                $table->boolean('requiere_tarjeta')->default(false);
                $table->timestamps();
            });

            // Seed inicial
            DB::table('metodos_pago')->insert([
                ['nombre' => 'Efectivo', 'requiere_tarjeta' => false, 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'Tarjeta', 'requiere_tarjeta' => true, 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'Reembolso', 'requiere_tarjeta' => false, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 2. Agregar campos a rendiciones
        Schema::table('rendiciones', function (Blueprint $table) {
            if (!Schema::hasColumn('rendiciones', 'fecha_aprobacion')) {
                $table->date('fecha_aprobacion')->nullable()->after('estado');
            }
            if (!Schema::hasColumn('rendiciones', 'observaciones')) {
                $table->text('observaciones')->nullable()->after('fecha_aprobacion');
            }
        });

        // 3. Agregar campos a rendicion_gastos
        Schema::table('rendicion_gastos', function (Blueprint $table) {
            if (Schema::hasColumn('rendicion_gastos', 'metodo_pago')) {
                $table->dropColumn('metodo_pago');
            }
            if (!Schema::hasColumn('rendicion_gastos', 'metodo_pago_id')) {
                $table->unsignedBigInteger('metodo_pago_id')->nullable()->after('monto');
                $table->foreign('metodo_pago_id')->references('id')->on('metodos_pago')->onDelete('set null');
            }
            if (!Schema::hasColumn('rendicion_gastos', 'tarjeta_ultimos_4')) {
                $table->string('tarjeta_ultimos_4', 4)->nullable()->after('metodo_pago_id');
            }
        });
    }

    public function down()
    {
        Schema::table('rendicion_gastos', function (Blueprint $table) {
            $table->dropForeign(['metodo_pago_id']);
            $table->dropColumn(['metodo_pago_id', 'tarjeta_ultimos_4']);
            $table->string('metodo_pago')->after('monto');
        });

        Schema::table('rendiciones', function (Blueprint $table) {
            $table->dropColumn(['fecha_aprobacion', 'observaciones']);
        });

        Schema::dropIfExists('metodos_pago');
    }
};
