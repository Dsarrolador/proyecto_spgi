<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conduce extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'requerimiento_id',
        'cliente_id',
        'contacto_id',
        'contacto_nombre',
        'fecha',
        'trabajo_realizar',
        'observaciones',
        'hora_entrada',
        'hora_salida',
        'cantidad_horas',
    ];

    public function requirement()
    {
        return $this->belongsTo(RequerimientoCliente::class, 'requerimiento_id');
    }

    public function cliente()
    {
        return $this->belongsTo(ClienteMaestro::class, 'cliente_id');
    }

    public function contacto()
    {
        return $this->belongsTo(LibretaContacto::class, 'contacto_id');
    }

    public function items()
    {
        return $this->hasMany(ConduceItem::class, 'conduce_id');
    }
}
