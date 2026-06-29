<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoRentabilidadGasto extends Model
{
    protected $table = 'proyecto_rentabilidad_gastos';

    protected $fillable = [
        'proyecto_rentabilidad_id',
        'fecha',
        'factura',
        'cuenta',
        'proveedor',
        'concepto',
        'monto',
        'clasificacion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    public function rentabilidad()
    {
        return $this->belongsTo(ProyectoRentabilidad::class, 'proyecto_rentabilidad_id');
    }
}
