<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatTipoEquipo extends Model
{
    protected $table = 'cat_tipos_equipo';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];
}
