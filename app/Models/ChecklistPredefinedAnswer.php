<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistPredefinedAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'respuesta',
        'puntos',
        'observacion_default',
        'recomendacion_default'
    ];

    public function question()
    {
        return $this->belongsTo(ChecklistQuestion::class, 'question_id');
    }
}
