<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteMaestro extends Model
{
    protected $table = 'cliente_maestro';

    protected $fillable = [
        'nombre',
        'rnc',
        'telefono_principal',
        'clasificacion_negocio',
        'clasificacion_interna',
        'categoria_iguala', // ✅ NUEVO
        'direccion_escrita',
        'notas',
    ];

    public function categoria()
    {
        // FK en cliente_maestro = clasificacion_interna
        // PK en categorias = id
        return $this->belongsTo(Categoria::class, 'clasificacion_interna', 'id');
    }

    public function contactos()
    {
        // (si ya la tienes, déjala como está)
        return $this->hasMany(LibretaContacto::class, 'codigo_cliente_maestro', 'id');
    }
}
