<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cliente_maestro', function (Blueprint $table) {
            if (!Schema::hasColumn('cliente_maestro', 'categoria_iguala')) {
                $table->string('categoria_iguala', 60)
                    ->nullable()
                    ->after('clasificacion_interna');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cliente_maestro', function (Blueprint $table) {
            if (Schema::hasColumn('cliente_maestro', 'categoria_iguala')) {
                $table->dropColumn('categoria_iguala');
            }
        });
    }
};
