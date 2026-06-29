<?php
 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequerimientoProyectoTarea extends Model
{
    use HasFactory;

    protected $table = 'requerimiento_proyecto_tareas';

    protected $fillable = [
        'requerimiento_proyecto_id',
        'nombre',
        'completada',
    ];

    protected $casts = [
        'completada' => 'boolean',
    ];

    public function requerimientoProyecto()
    {
        return $this->belongsTo(RequerimientoProyecto::class, 'requerimiento_proyecto_id');
    }
}
