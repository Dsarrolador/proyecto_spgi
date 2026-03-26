<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {

            if (!Schema::hasColumn('roles', 'descripcion')) {
                $table->string('descripcion')->nullable()->after('nombre');
            }

            if (!Schema::hasColumn('roles', 'activo')) {
                $table->boolean('activo')->default(1)->after('descripcion');
            }

            if (!Schema::hasColumn('roles', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {

            if (Schema::hasColumn('roles', 'descripcion')) {
                $table->dropColumn('descripcion');
            }

            if (Schema::hasColumn('roles', 'activo')) {
                $table->dropColumn('activo');
            }

            if (Schema::hasColumn('roles', 'created_at')) {
                $table->dropTimestamps();
            }
        });
    }
};

