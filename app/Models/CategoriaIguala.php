<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaIguala extends Model
{
    protected $table = 'categorias_iguala';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];
}
