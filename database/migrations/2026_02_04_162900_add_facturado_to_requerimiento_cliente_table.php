<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            if (!Schema::hasColumn('requerimiento_cliente', 'facturado')) {
                $table->boolean('facturado')->default(false)->after('tiempo_invertido');
            }
        });
    }

    public function down(): void
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            if (Schema::hasColumn('requerimiento_cliente', 'facturado')) {
                $table->dropColumn('facturado');
            }
        });
    }
};
