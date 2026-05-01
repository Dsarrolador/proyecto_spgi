<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'filename',
        'path',
        'type',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
