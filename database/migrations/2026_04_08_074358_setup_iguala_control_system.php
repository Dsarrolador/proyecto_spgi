<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetupIgualaControlSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Categorizar tipos de soporte
        if (!Schema::hasColumn('tipo_soporte', 'clase')) {
            Schema::table('tipo_soporte', function (Blueprint $table) {
                $table->string('clase')->default('otro')->after('nombre'); // remoto, visita, otro
            });
        }

        // Data sync para tipo_soporte
        DB::table('tipo_soporte')->where('nombre', 'like', '%interno%')->update(['clase' => 'remoto']);
        DB::table('tipo_soporte')->where('nombre', 'like', '%externo%')->update(['clase' => 'visita']);

        // 2. Vincular clientes a sus categorías de iguala (por ID)
        Schema::table('cliente_maestro', function (Blueprint $table) {
            $table->unsignedBigInteger('categoria_iguala_id')->nullable()->after('categoria_iguala');
            $table->foreign('categoria_iguala_id')->references('id')->on('categorias_iguala');
        });

        // Data sync para cliente_maestro: intentar vincular por nombre de la iguala existente
        $clientes = DB::table('cliente_maestro')->get();
        foreach ($clientes as $cliente) {
            if ($cliente->categoria_iguala) {
                $iguala = DB::table('categorias_iguala')
                    ->where('nombre', 'like', '%' . $cliente->categoria_iguala . '%')
                    ->first();
                
                if ($iguala) {
                    DB::table('cliente_maestro')
                        ->where('id', $cliente->id)
                        ->update(['categoria_iguala_id' => $iguala->id]);
                }
            }
        }
    }

    public function down()
    {
        Schema::table('cliente_maestro', function (Blueprint $table) {
            $table->dropForeign(['categoria_iguala_id']);
            $table->dropColumn('categoria_iguala_id');
        });

        Schema::table('tipo_soporte', function (Blueprint $table) {
            $table->dropColumn('clase');
        });
    }
}
