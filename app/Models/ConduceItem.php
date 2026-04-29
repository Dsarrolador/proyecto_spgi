<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConduceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'conduce_id',
        'cantidad',
        'descripcion',
        'num_cotizacion',
        'facturar',
    ];

    protected $casts = [
        'facturar' => 'boolean',
    ];

    public function conduce()
    {
        return $this->belongsTo(Conduce::class, 'conduce_id');
    }
}
