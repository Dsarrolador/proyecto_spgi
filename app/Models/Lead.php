<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'persona_contacto',
        'direccion',
        'contacto',
        'correo',
        'cotizacion_pdf',
        'total_estimado',
        'calculo_data',
        'observaciones',
        'status',
        'user_id',
    ];

    protected $casts = [
        'calculo_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requirements()
    {
        return $this->hasMany(LeadRequirement::class);
    }
}
