<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteNovedadAdmin extends Model
{
    protected $table = 'cliente_novedades_admin';

    protected $fillable = [
        'cliente_id',
        'user_id',
        'fecha',
        'medio',
        'detalle',
    ];

    protected $casts = [
        'fecha' => 'datetime',
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
