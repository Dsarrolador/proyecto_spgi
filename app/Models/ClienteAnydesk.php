<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteAnydesk extends Model
{
    protected $table = 'cliente_anydesks';

    protected $fillable = [
        'cliente_id',
        'anydesk_id',
        'alias',
        'notas',
    ];

    public function cliente()
    {
        return $this->belongsTo(ClienteMaestro::class, 'cliente_id');
    }
}
