<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedCategoriasIgualaData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $data = [
            [
                'nombre' => 'Cliente de iguala VIP',
                'descripcion' => 'Plan de alta prioridad con máximos beneficios.',
                'activo' => 1,
                'cantidad_soporte_remoto' => 10,
                'cantidad_visitas' => 5,
                'mantenimiento_sw_hw' => 1,
                'equipo_prestamo' => 1,
                'asistencia_vip' => 1,
            ],
            [
                'nombre' => 'Cliente de iguala Especial',
                'descripcion' => 'Plan intermedio para clientes con necesidades específicas.',
                'activo' => 1,
                'cantidad_soporte_remoto' => 5,
                'cantidad_visitas' => 2,
                'mantenimiento_sw_hw' => 1,
                'equipo_prestamo' => 0,
                'asistencia_vip' => 0,
            ],
            [
                'nombre' => 'Cliente de iguala Basico',
                'descripcion' => 'Plan esencial para mantenimiento preventivo.',
                'activo' => 1,
                'cantidad_soporte_remoto' => 2,
                'cantidad_visitas' => 1,
                'mantenimiento_sw_hw' => 0,
                'equipo_prestamo' => 0,
                'asistencia_vip' => 0,
            ],
            [
                'nombre' => 'Cliente sin iguala',
                'descripcion' => 'Clientes ocasionales sin contrato de mantenimiento fijo.',
                'activo' => 1,
                'cantidad_soporte_remoto' => 0,
                'cantidad_visitas' => 0,
                'mantenimiento_sw_hw' => 0,
                'equipo_prestamo' => 0,
                'asistencia_vip' => 0,
            ],
        ];

        foreach ($data as $row) {
            DB::table('categorias_iguala')->updateOrInsert(
                ['nombre' => $row['nombre']],
                array_merge($row, ['updated_at' => now(), 'created_at' => now()])
            );
        }
    }

    public function down()
    {
        DB::table('categorias_iguala')->whereIn('nombre', [
            'Cliente de iguala VIP',
            'Cliente de iguala Especial',
            'Cliente de iguala Basico',
            'Cliente sin iguala'
        ])->delete();
    }
}
