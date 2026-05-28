<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NovedadRequerimientoProyecto extends Model
{
    use HasFactory;

<<<<<<< HEAD
    protected $table = 'novedades_proyectos';
=======
    protected $table = 'novedades_requerimientos_proyectos';
>>>>>>> master

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

<<<<<<< HEAD
    public function requerimientoProyecto()
=======
    public function requerimiento()
>>>>>>> master
    {
        return $this->belongsTo(
            RequerimientoProyecto::class,
            'requerimiento_proyecto_id'
        );
    }
<<<<<<< HEAD
=======

    public function cliente()
    {
        return $this->belongsTo(
            ClienteMaestro::class,
            'cliente_id'
        );
    }
>>>>>>> master
}
