<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitaRespuesta extends Model
{
    use HasFactory;

    protected $table = 'visita_respuestas';

    protected $fillable = [
        'visita_id',
        'question_id',
        'respuesta_seleccionada',
        'puntos',
        'observaciones',
        'recomendacion'
    ];

    public function visita()
    {
        return $this->belongsTo(Visita::class, 'visita_id');
    }

    public function question()
    {
        return $this->belongsTo(ChecklistQuestion::class, 'question_id');
    }
}
