<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoraExtraDetalle extends Model
{
    use HasFactory;

    protected $table = 'horas_extras_detalles';

    protected $fillable = [
        'hora_extra_id',
        'fecha',
        'colaborador',
        'concepto',
        'hora_inicio',
        'hora_salida',
        'total_horas',
        'tarifa_hora',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function horaExtra()
    {
        return $this->belongsTo(HoraExtra::class, 'hora_extra_id');
    }

    public function getSubtotalAttribute()
    {
        return $this->total_horas * $this->tarifa_hora;
    }
}
