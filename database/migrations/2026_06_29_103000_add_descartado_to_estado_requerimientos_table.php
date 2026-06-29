<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('estado_requerimientos')->where('nombre', 'Descartado')->exists();
        if (!$exists) {
            DB::table('estado_requerimientos')->insert([
                'nombre' => 'Descartado',
                'color' => 'bg-danger',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function down(): void
    {
        DB::table('estado_requerimientos')->where('nombre', 'Descartado')->delete();
    }
};
