<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CumpleanosUsuario extends Model
{
    use HasFactory;

    protected $table = 'cumpleanos_usuarios';

    protected $fillable = [
        'user_id',
        'fecha_nacimiento'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
