<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'template_id',
        'user_id',
        'total_puntos',
        'estado_cliente',
        'accion_sugerida'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function template()
    {
        return $this->belongsTo(ChecklistTemplate::class, 'template_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(LeadChecklistAnswer::class, 'lead_checklist_id');
    }
}
