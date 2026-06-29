<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoRentabilidadHoraExtra extends Model
{
    protected $table = 'proyecto_rentabilidad_horas_extras';

    protected $fillable = [
        'proyecto_rentabilidad_id',
        'fecha',
        'colaborador',
        'salario_mensual',
        'salario_diario',
        'salario_por_hora',
        'al_100',
        'total',
        'cantidad_horas',
        'total_pagar',
    ];

    protected $casts = [
        'fecha' => 'date',
        'salario_mensual' => 'decimal:2',
        'salario_diario' => 'decimal:2',
        'salario_por_hora' => 'decimal:2',
        'al_100' => 'decimal:2',
        'total' => 'decimal:2',
        'cantidad_horas' => 'decimal:2',
        'total_pagar' => 'decimal:2',
    ];

    public function rentabilidad()
    {
        return $this->belongsTo(ProyectoRentabilidad::class, 'proyecto_rentabilidad_id');
    }
}
