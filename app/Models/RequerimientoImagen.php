<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequerimientoImagen extends Model
{
    protected $table = 'requerimiento_imagenes';

    protected $fillable = [
        'requerimiento_id',
        'imagen'
    ];

    public function requerimiento()
    {
        return $this->belongsTo(RequerimientoCliente::class);
    }
}
