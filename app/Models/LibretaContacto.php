<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibretaContacto extends Model
{
    use HasFactory;

    protected $table = 'libreta_contacto';

    /**
     * La tabla NO tiene created_at / updated_at
     */
    public $timestamps = false;

    protected $fillable = [
        'codigo_cliente_maestro',
        'nombre',
        'telefono',
        'correo',
        'fecha_nacimiento',
        'nota',
        'codigo_rol',
    ];

    /* =========================
     * RELACIONES
     * ========================= */

    public function cliente()
    {
        return $this->belongsTo(ClienteMaestro::class, 'codigo_cliente_maestro', 'id');
    }

    public function rol()
    {
        return $this->belongsTo(Roles::class, 'codigo_rol', 'id');
    }
}
