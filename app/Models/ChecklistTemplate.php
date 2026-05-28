<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];

    public function questions()
    {
        return $this->hasMany(ChecklistQuestion::class, 'template_id')->orderBy('orden');
    }
}
