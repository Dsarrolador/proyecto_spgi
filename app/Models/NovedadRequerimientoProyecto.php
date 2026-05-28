<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NovedadRequerimientoProyecto extends Model
{
    use HasFactory;

    protected $table = 'novedades_requerimientos_proyectos';

    protected $fillable = [
        'requerimiento_proyecto_id',
        'cliente_id',
        'user_id',
        'novedad',
        'tipo',
        'adjunto',
        'nombre_original'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function requerimiento()
    {
        return $this->belongsTo(
            RequerimientoProyecto::class,
            'requerimiento_proyecto_id'
        );
    }

    public function cliente()
    {
        return $this->belongsTo(
            ClienteMaestro::class,
            'cliente_id'
        );
    }
}
