<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categoria';

    // ✅ Tu tabla no tiene created_at/updated_at
    public $timestamps = false;

    protected $fillable = ['categoria'];

    public function clientes()
    {
        return $this->hasMany(\App\Models\ClienteMaestro::class, 'clasificacion_interna', 'id');
    }
}