<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NovedadRequerimiento extends Model
{
    use HasFactory;

    // 👇 NOMBRE REAL DE TU TABLA
    protected $table = 'novedades_requerimientos';

    protected $fillable = [
        'requerimiento_id',
        'cliente_id',
        'user_id',
        'novedad',
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
            RequerimientoCliente::class,
            'requerimiento_id'
        );
    }
}
