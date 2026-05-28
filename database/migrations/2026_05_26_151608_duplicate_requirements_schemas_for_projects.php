<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DuplicateRequirementsSchemasForProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Modificar requerimiento_proyecto
        Schema::table('requerimiento_proyecto', function (Blueprint $table) {
            $table->unsignedBigInteger('asignado_user_id')->nullable()->after('user_id');
            $table->string('archivo_factura')->nullable()->after('facturado');
            $table->boolean('es_recurrente')->default(false)->after('archivo_factura');
            $table->string('frecuencia')->nullable()->after('es_recurrente');
            $table->dateTime('proxima_fecha_ejecucion')->nullable()->after('frecuencia');
            $table->dateTime('fecha_inicio_recurrencia')->nullable()->after('proxima_fecha_ejecucion');
            $table->boolean('es_colaborativo')->default(false)->after('fecha_inicio_recurrencia');

            $table->foreign('asignado_user_id')
                  ->references('id')->on('users')
                  ->onDelete('set null');
        });

        // 2. Crear novedades_requerimientos_proyectos
        Schema::create('novedades_requerimientos_proyectos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requerimiento_proyecto_id');
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('novedad');
            $table->string('tipo')->nullable();
            $table->string('adjunto')->nullable();
            $table->string('nombre_original')->nullable();
            $table->timestamps();

            $table->foreign('requerimiento_proyecto_id', 'fk_nov_req_proj_req')
                  ->references('id')->on('requerimiento_proyecto')
                  ->onDelete('cascade');

            $table->foreign('cliente_id', 'fk_nov_req_proj_cli')
                  ->references('id')->on('cliente_maestro')
                  ->onDelete('cascade');

            $table->foreign('user_id', 'fk_nov_req_proj_user')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });

        // 3. Crear requerimiento_proyecto_imagenes
        Schema::create('requerimiento_proyecto_imagenes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requerimiento_proyecto_id');
            $table->string('imagen');
            $table->timestamps();

            $table->foreign('requerimiento_proyecto_id', 'fk_req_proj_img_req')
                  ->references('id')->on('requerimiento_proyecto')
                  ->onDelete('cascade');
        });

        // 4. Crear requerimiento_proyecto_colaboradores
        Schema::create('requerimiento_proyecto_colaboradores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requerimiento_proyecto_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('requerimiento_proyecto_id', 'fk_req_proj_colab_req')
                  ->references('id')->on('requerimiento_proyecto')
                  ->onDelete('cascade');

            $table->foreign('user_id', 'fk_req_proj_colab_user')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requerimiento_proyecto_colaboradores');
        Schema::dropIfExists('requerimiento_proyecto_imagenes');
        Schema::dropIfExists('novedades_requerimientos_proyectos');

        Schema::table('requerimiento_proyecto', function (Blueprint $table) {
            $table->dropForeign(['asignado_user_id']);
            
            $table->dropColumn([
                'asignado_user_id',
                'archivo_factura',
                'es_recurrente',
                'frecuencia',
                'proxima_fecha_ejecucion',
                'fecha_inicio_recurrencia',
                'es_colaborativo'
            ]);
        });
    }
}
