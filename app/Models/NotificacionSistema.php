<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacionSistema extends Model
{
    protected $table = 'notificaciones_sistema';

    protected $fillable = [
        'user_id',
        'sender_id',
        'mensaje',
        'leido_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
