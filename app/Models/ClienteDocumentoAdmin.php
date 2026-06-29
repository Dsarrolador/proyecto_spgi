<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteDocumentoAdmin extends Model
{
    protected $table = 'cliente_documentos_admin';

    protected $fillable = [
        'cliente_id',
        'nombre',
        'archivo_path',
    ];

    public function cliente()
    {
        return $this->belongsTo(ClienteMaestro::class, 'cliente_id');
    }
}
