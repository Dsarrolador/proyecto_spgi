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
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            if (!Schema::hasColumn('requerimiento_cliente', 'notas_internas')) {
                $table->text('notas_internas')->nullable();
            }
            if (!Schema::hasColumn('requerimiento_cliente', 'notas_clientes')) {
                $table->text('notas_clientes')->nullable();
            }
            if (!Schema::hasColumn('requerimiento_cliente', 'notas_last_user_id')) {
                $table->unsignedBigInteger('notas_last_user_id')->nullable();
                $table->foreign('notas_last_user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('set null');
            }
            if (!Schema::hasColumn('requerimiento_cliente', 'notas_seen')) {
                $table->boolean('notas_seen')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requerimiento_cliente', function (Blueprint $table) {
            if (Schema::hasColumn('requerimiento_cliente', 'notas_last_user_id')) {
                $table->dropForeign(['notas_last_user_id']);
                $table->dropColumn(['notas_last_user_id']);
            }
            if (Schema::hasColumn('requerimiento_cliente', 'notas_internas')) {
                $table->dropColumn(['notas_internas']);
            }
            if (Schema::hasColumn('requerimiento_cliente', 'notas_clientes')) {
                $table->dropColumn(['notas_clientes']);
            }
            if (Schema::hasColumn('requerimiento_cliente', 'notas_seen')) {
                $table->dropColumn(['notas_seen']);
            }
        });
    }
};
