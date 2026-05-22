<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rendicion extends Model
{
    use HasFactory;

    protected $table = 'rendiciones';

    protected $fillable = [
        'user_id',
        'responsable_id',
        'titulo',
        'estado',
        'fecha_aprobacion',
        'observaciones',
    ];

    protected $casts = [
        'fecha_aprobacion' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function gastos()
    {
        return $this->hasMany(RendicionGasto::class, 'rendicion_id');
    }

    public function getTotalAttribute()
    {
        return $this->gastos()->sum('monto');
    }
}
