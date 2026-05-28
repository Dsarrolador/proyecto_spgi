<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;

    protected $table = 'visitas';

    protected $fillable = [
        'nombre_visitado',
        'correo_visitado',
        'nombre_recibio',
        'telefono_recibio',
        'template_id',
        'user_id',
        'total_puntos',
        'estado_cliente',
        'accion_sugerida'
    ];

    public function template()
    {
        return $this->belongsTo(ChecklistTemplate::class, 'template_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function respuestas()
    {
        return $this->hasMany(VisitaRespuesta::class, 'visita_id');
    }
}
