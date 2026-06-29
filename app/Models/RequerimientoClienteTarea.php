<?php
 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequerimientoClienteTarea extends Model
{
    use HasFactory;

    protected $table = 'requerimiento_cliente_tareas';

    protected $fillable = [
        'requerimiento_cliente_id',
        'nombre',
        'completada',
    ];

    protected $casts = [
        'completada' => 'boolean',
    ];

    public function requerimientoCliente()
    {
        return $this->belongsTo(RequerimientoCliente::class, 'requerimiento_cliente_id');
    }
}
