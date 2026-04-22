<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteEquipo extends Model
{
    protected $table = 'cliente_equipos';

    protected $fillable = [
        'cliente_id',
        'cat_equipo_id',
        'parent_id',
        'wiki_document_id',
        'driver_id',
        'driver_nombre',
        'extra_system_id',
        'extra_system_nombre',
        'alias',
        'serie',
        'configuracion_especifica',
        'notas',
    ];

    public function cliente()
    {
        return $this->belongsTo(ClienteMaestro::class, 'cliente_id');
    }

    public function catalogo()
    {
        return $this->belongsTo(CatEquipo::class, 'cat_equipo_id');
    }

    public function peripherals()
    {
        return $this->hasMany(ClienteEquipo::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(ClienteEquipo::class, 'parent_id');
    }

    public function wikiDocument()
    {
        return $this->belongsTo(WikiDocument::class, 'wiki_document_id');
    }

    public function driver()
    {
        return $this->belongsTo(WikiDocument::class, 'driver_id');
    }

    public function extraSystem()
    {
        return $this->belongsTo(WikiDocument::class, 'extra_system_id');
    }
}
