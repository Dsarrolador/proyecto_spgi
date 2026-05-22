<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoraExtra extends Model
{
    use HasFactory;

    protected $table = 'horas_extras';

    protected $fillable = [
        'titulo',
        'fecha_registro',
        'estado',
        'user_id',
        'responsable_id',
        'observaciones',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function detalles()
    {
        return $this->hasMany(HoraExtraDetalle::class, 'hora_extra_id');
    }

    public function getTotalHorasAttribute()
    {
        return $this->detalles()->sum('total_horas');
    }

    public function getTotalMontoAttribute()
    {
        return $this->detalles()->get()->sum(function ($detalle) {
            return $detalle->total_horas * $detalle->tarifa_hora;
        });
    }
}
