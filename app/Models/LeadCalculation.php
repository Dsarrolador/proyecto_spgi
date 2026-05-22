<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'nombre',
        'total_estimado',
        'status',
        'calculo_data',
    ];

    protected $casts = [
        'calculo_data' => 'array',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function files()
    {
        return $this->hasMany(LeadFile::class, 'calculation_id');
    }
}
