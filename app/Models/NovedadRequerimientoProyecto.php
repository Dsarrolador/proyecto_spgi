<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NovedadRequerimientoProyecto extends Model
{
    use HasFactory;

    // Tabla creada en migracion 2026_05_28_180000_create_novedades_proyectos_table
    protected $table = 'novedades_proyectos';

    protected $fillable = [
        'requerimiento_proyecto_id',
        'cliente_id',
        'user_id',
        'novedad',
        'tipo',
        'adjunto',
        'nombre_original',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Alias principal (feature branch) */
    public function requerimientoProyecto()
    {
        return $this->belongsTo(
            RequerimientoProyecto::class,
            'requerimiento_proyecto_id'
        );
    }

    /** Alias secundario (master) */
    public function requerimiento()
    {
        return $this->requerimientoProyecto();
    }

    public function cliente()
    {
        return $this->belongsTo(
            ClienteMaestro::class,
            'cliente_id'
        );
    }
}