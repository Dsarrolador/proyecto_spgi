<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaIguala extends Model
{
    protected $table = 'categorias_iguala';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
        'cantidad_soporte_remoto',
        'cantidad_visitas',
        'mantenimiento_sw_hw',
        'equipo_prestamo',
        'asistencia_vip',
    ];
}
