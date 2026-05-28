<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadChecklistAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_checklist_id',
        'question_id',
        'respuesta_seleccionada',
        'puntos',
        'observaciones',
        'recomendacion'
    ];

    public function checklist()
    {
        return $this->belongsTo(LeadChecklist::class, 'lead_checklist_id');
    }

    public function question()
    {
        return $this->belongsTo(ChecklistQuestion::class, 'question_id');
    }
}
