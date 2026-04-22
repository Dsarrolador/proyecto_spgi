<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatEquipo extends Model
{
    protected $table = 'cat_equipos';

    protected $fillable = [
        'nombre',
        'tipo_equipo_id',
        'tipo',
        'marca',
        'modelo',
        'caracteristicas',
        'configuracion_estandar',
        'driver_url',
        'driver_doc_id',
        'activo',
    ];

    public function tipoEquipo()
    {
        return $this->belongsTo(CatTipoEquipo::class, 'tipo_equipo_id');
    }

    public function asignaciones()
    {
        return $this->hasMany(ClienteEquipo::class, 'cat_equipo_id');
    }

    public function driverDoc()
    {
        return $this->belongsTo(WikiDocument::class, 'driver_doc_id');
    }
}
