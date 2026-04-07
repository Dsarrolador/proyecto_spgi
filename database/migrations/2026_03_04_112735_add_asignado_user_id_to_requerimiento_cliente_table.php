<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            if (!Schema::hasColumn('requerimiento_cliente', 'asignado_user_id')) {
                $table->unsignedBigInteger('asignado_user_id')->nullable()->after('user_id');

                $table->foreign('asignado_user_id')
                    ->references('id')->on('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            if (Schema::hasColumn('requerimiento_cliente', 'asignado_user_id')) {
                $table->dropForeign(['asignado_user_id']);
                $table->dropColumn('asignado_user_id');
            }
        });
    }
};