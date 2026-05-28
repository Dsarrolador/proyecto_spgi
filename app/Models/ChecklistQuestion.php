<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['template_id', 'pregunta', 'orden'];

    public function template()
    {
        return $this->belongsTo(ChecklistTemplate::class, 'template_id');
    }

    public function predefinedAnswers()
    {
        return $this->hasMany(ChecklistPredefinedAnswer::class, 'question_id');
    }
}
