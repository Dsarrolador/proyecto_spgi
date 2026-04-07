<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use HasFactory;

    protected $table = 'roles'; // 👈 Nombre real de la tabla en tu BD
    public $timestamps = false;

    protected $fillable = [
        'NOMBRE',
    ];
}
