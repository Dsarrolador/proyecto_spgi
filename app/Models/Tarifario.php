<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarifario extends Model
{
    use HasFactory;

    protected $fillable = [
        'descripcion',
        'basico_int',
        'avanzado_int',
        'basico_ext',
        'avanzado_ext',
        'valor',
        'tipo_tarifario_id',
    ];

    public function tipoTarifario()
    {
        return $this->belongsTo(\App\Models\TipoTarifario::class, 'tipo_tarifario_id');
    }
}
