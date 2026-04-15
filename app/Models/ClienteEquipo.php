<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteEquipo extends Model
{
    protected $table = 'cliente_equipos';

    protected $fillable = [
        'cliente_id',
        'cat_equipo_id',
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
}
