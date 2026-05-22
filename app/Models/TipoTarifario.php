<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoTarifario extends Model
{
    use HasFactory;

    protected $table = 'tipo_tarifarios';

    protected $fillable = [
        'nombre',
    ];

    public function tarifarios()
    {
        return $this->hasMany(Tarifario::class, 'tipo_tarifario_id');
    }
}
