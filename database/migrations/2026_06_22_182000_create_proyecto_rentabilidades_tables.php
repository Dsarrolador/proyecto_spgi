<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProyectoRentabilidadesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabla principal de Rentabilidad de Proyectos
        Schema::create('proyecto_rentabilidades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyecto_id');
            $table->date('fecha_analisis');
            $table->decimal('comision_porcentaje', 5, 2)->default(10.00);
            $table->unsignedBigInteger('comision_user_id')->nullable(); // Comercial a comisionar
            $table->timestamps();

            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');
            $table->foreign('comision_user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Tabla de Proyecciones de Cotizaciones
        Schema::create('proyecto_rentabilidad_proyecciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyecto_rentabilidad_id');
            $table->string('cotizacion_no')->nullable();
            $table->string('referencia');
            $table->decimal('abono', 15, 2)->default(0.00);
            $table->decimal('equipos_materiales', 15, 2)->default(0.00);
            $table->decimal('honorarios', 15, 2)->default(0.00);
            $table->decimal('itbis', 15, 2)->default(0.00);
            $table->decimal('total_facturado', 15, 2)->default(0.00);
            $table->decimal('total_adeudado', 15, 2)->default(0.00);
            $table->date('fecha_pago')->nullable();
            $table->timestamps();

            $table->foreign('proyecto_rentabilidad_id', 'fk_rentabilidad_proy_id')
                  ->references('id')->on('proyecto_rentabilidades')
                  ->onDelete('cascade');
        });

        // Tabla de Gastos
        Schema::create('proyecto_rentabilidad_gastos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyecto_rentabilidad_id');
            $table->date('fecha');
            $table->string('factura')->nullable();
            $table->string('cuenta')->nullable();
            $table->string('proveedor');
            $table->string('concepto');
            $table->decimal('monto', 15, 2);
            $table->string('clasificacion'); // Honorario a Terceros, Uso Interno, Viáticos, Transporte, Equipo
            $table->timestamps();

            $table->foreign('proyecto_rentabilidad_id', 'fk_rentabilidad_gastos_id')
                  ->references('id')->on('proyecto_rentabilidades')
                  ->onDelete('cascade');
        });

        // Tabla de Horas Extras
        Schema::create('proyecto_rentabilidad_horas_extras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyecto_rentabilidad_id');
            $table->date('fecha');
            $table->string('colaborador');
            $table->decimal('salario_mensual', 15, 2);
            $table->decimal('salario_diario', 15, 2);
            $table->decimal('salario_por_hora', 15, 2);
            $table->decimal('al_100', 8, 2)->default(100.00);
            $table->decimal('total', 15, 2); // Salario por hora al 100% (Calculado)
            $table->decimal('cantidad_horas', 8, 2);
            $table->decimal('total_pagar', 15, 2);
            $table->timestamps();

            $table->foreign('proyecto_rentabilidad_id', 'fk_rentabilidad_he_id')
                  ->references('id')->on('proyecto_rentabilidades')
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
        Schema::dropIfExists('proyecto_rentabilidad_horas_extras');
        Schema::dropIfExists('proyecto_rentabilidad_gastos');
        Schema::dropIfExists('proyecto_rentabilidad_proyecciones');
        Schema::dropIfExists('proyecto_rentabilidades');
    }
}
