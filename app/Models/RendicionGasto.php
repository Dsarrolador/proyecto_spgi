<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendicionGasto extends Model
{
    use HasFactory;

    protected $table = 'rendicion_gastos';

    protected $fillable = [
        'rendicion_id',
        'fecha',
        'concepto',
        'proveedor',
        'monto',
        'metodo_pago_id',
        'tarjeta_ultimos_4',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function rendicion()
    {
        return $this->belongsTo(Rendicion::class, 'rendicion_id');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id');
    }
}
