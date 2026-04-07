<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProyectosTableAddFields extends Migration
{
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {

            if (!Schema::hasColumn('proyectos', 'cliente_id')) {
                $table->unsignedBigInteger('cliente_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('proyectos', 'contacto_id')) {
                $table->unsignedBigInteger('contacto_id')->nullable()->after('cliente_id');
            }

            if (!Schema::hasColumn('proyectos', 'tipo_proyecto')) {
                $table->string('tipo_proyecto', 80)->nullable()->after('contacto_id');
            }

            if (!Schema::hasColumn('proyectos', 'fecha_inicio')) {
                $table->date('fecha_inicio')->nullable()->after('descripcion');
            }

            if (!Schema::hasColumn('proyectos', 'fecha_fin')) {
                $table->date('fecha_fin')->nullable()->after('fecha_inicio');
            }

            if (!Schema::hasColumn('proyectos', 'prioridad')) {
                $table->string('prioridad', 20)->default('Media')->after('fecha_fin');
            }

            if (!Schema::hasColumn('proyectos', 'estado')) {
                $table->string('estado', 20)->default('Activo')->after('prioridad');
            }

            if (!Schema::hasColumn('proyectos', 'adjunto')) {
                $table->string('adjunto')->nullable()->after('estado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {

            $columns = [
                'cliente_id',
                'contacto_id',
                'tipo_proyecto',
                'fecha_inicio',
                'fecha_fin',
                'prioridad',
                'estado',
                'adjunto'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('proyectos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
