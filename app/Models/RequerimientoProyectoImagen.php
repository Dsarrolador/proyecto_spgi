<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequerimientoProyectoImagen extends Model
{
    use HasFactory;

    protected $table = 'requerimiento_proyecto_imagenes';

    protected $fillable = [
        'requerimiento_proyecto_id',
        'imagen'
    ];

    public function requerimiento()
    {
        return $this->belongsTo(
            RequerimientoProyecto::class,
            'requerimiento_proyecto_id'
        );
    }
}
