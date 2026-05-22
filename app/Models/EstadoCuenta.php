<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EstadoCuenta extends Model
{
    use HasFactory;

    protected $table = 'estado_cuentas';

    protected $fillable = [
        'cliente_maestro_id',
        'cliente_nombre',
        'factura_no',
        'nfc',
        'fecha',
        'fecha_vencimiento',
        'producto',
        'balance',
        'moneda',
        'tasa_cambio',
        'fecha_pago',
        'fecha_aplicado',
        'recibo_no',
        'total_pagado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'date',
        'fecha_aplicado' => 'date',
        'balance' => 'decimal:2',
        'tasa_cambio' => 'decimal:2',
        'total_pagado' => 'decimal:2',
    ];

    public function clienteMaestro()
    {
        return $this->belongsTo(ClienteMaestro::class, 'cliente_maestro_id');
    }

    /**
     * Get dynamic status: PAGO, VENCIDO, PENDIENTE
     */
    public function getEstadoCalculadoAttribute()
    {
        if ($this->fecha_pago) {
            return 'PAGO';
        }

        $hoy = Carbon::today();
        if ($this->fecha_vencimiento->lt($hoy)) {
            return 'VENCIDO';
        }

        return 'PENDIENTE';
    }

    /**
     * Get dynamic overdue or remaining days
     */
    public function getDiasAttribute()
    {
        if ($this->fecha_pago) {
            return null; // Paid invoices don't show remaining/overdue days in the Excel sheet
        }

        $hoy = Carbon::today();
        
        // Carbon diffInDays will give absolute value, so we must calculate direction
        if ($this->fecha_vencimiento->lt($hoy)) {
            // Overdue: negative days
            return - $hoy->diffInDays($this->fecha_vencimiento);
        } else {
            // Pending: positive days remaining
            return $this->fecha_vencimiento->diffInDays($hoy);
        }
    }
}
