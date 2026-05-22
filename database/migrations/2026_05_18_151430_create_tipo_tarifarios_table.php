<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tipo_tarifarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        // Insert initial data
        DB::table('tipo_tarifarios')->insert([
            ['nombre' => 'Soporte', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Honorario Profesional', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('tarifarios', function (Blueprint $table) {
            $table->dropForeign(['tipo_soporte_id']);
            $table->dropColumn('tipo_soporte_id');
            $table->unsignedBigInteger('tipo_tarifario_id')->nullable()->after('id');
            $table->foreign('tipo_tarifario_id')->references('id')->on('tipo_tarifarios')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('tarifarios', function (Blueprint $table) {
            $table->dropForeign(['tipo_tarifario_id']);
            $table->dropColumn('tipo_tarifario_id');
            $table->unsignedBigInteger('tipo_soporte_id')->nullable()->after('id');
            $table->foreign('tipo_soporte_id')->references('id')->on('tipo_soporte')->onDelete('set null');
        });

        Schema::dropIfExists('tipo_tarifarios');
    }
};
