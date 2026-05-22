<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    use HasFactory;

    protected $table = 'metodos_pago';

    protected $fillable = [
        'nombre',
        'requiere_tarjeta',
    ];

    public function gastos()
    {
        return $this->hasMany(RendicionGasto::class, 'metodo_pago_id');
    }
}
