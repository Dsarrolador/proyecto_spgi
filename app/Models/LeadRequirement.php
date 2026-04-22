<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadRequirement extends Model
{
    use HasFactory;

    protected $fillable = ['lead_id', 'descripcion', 'estado', 'user_id', 'asignado_id'];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function asignado()
    {
        return $this->belongsTo(User::class, 'asignado_id');
    }
}
