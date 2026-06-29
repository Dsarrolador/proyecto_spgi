<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoRentabilidadProyeccion extends Model
{
    protected $table = 'proyecto_rentabilidad_proyecciones';

    protected $fillable = [
        'proyecto_rentabilidad_id',
        'cotizacion_no',
        'referencia',
        'abono',
        'equipos_materiales',
        'honorarios',
        'itbis',
        'total_facturado',
        'total_adeudado',
        'fecha_pago',
    ];

    protected $casts = [
        'abono' => 'decimal:2',
        'equipos_materiales' => 'decimal:2',
        'honorarios' => 'decimal:2',
        'itbis' => 'decimal:2',
        'total_facturado' => 'decimal:2',
        'total_adeudado' => 'decimal:2',
        'fecha_pago' => 'date',
    ];

    public function rentabilidad()
    {
        return $this->belongsTo(ProyectoRentabilidad::class, 'proyecto_rentabilidad_id');
    }
}
