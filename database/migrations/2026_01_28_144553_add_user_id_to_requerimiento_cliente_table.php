<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('estado');

            // FK (si tu tabla de usuarios se llama "users" y el id es bigint)
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
