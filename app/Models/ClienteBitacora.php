<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteBitacora extends Model
{
    protected $table = 'cliente_bitacoras';

    protected $fillable = [
        'cliente_id',
        'user_id',
        'nota',
    ];

    public function cliente()
    {
        return $this->belongsTo(ClienteMaestro::class, 'cliente_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
