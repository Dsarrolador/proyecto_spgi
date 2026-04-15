<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ClienteEntornoDocumento extends Model
{
    protected $table = 'cliente_entorno_documentos';

    protected $fillable = [
        'cliente_id',
        'tipo',
        'nombre',
        'archivo_path',
        'url',
        'usuario',
        'clave',
    ];

    public function cliente()
    {
        return $this->belongsTo(ClienteMaestro::class, 'cliente_id');
    }

    // Accesor para desencriptar clave
    public function getClaveDesencriptadaAttribute()
    {
        try {
            return $this->clave ? Crypt::decryptString($this->clave) : '';
        } catch (\Exception $e) {
            return $this->clave; // Por si hay datos legacy no cifrados
        }
    }

    // Mutador para encriptar clave
    public function setClaveAttribute($value)
    {
        $this->attributes['clave'] = $value ? Crypt::encryptString($value) : null;
    }
}
