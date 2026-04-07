<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoSoporte extends Model
{
    use HasFactory;

    protected $table = 'tipo_soporte';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];
}
