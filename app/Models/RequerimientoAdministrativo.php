<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequerimientoAdministrativo extends Model
{
    protected $table = 'requerimientos_administrativos';

    protected $fillable = [
        'titulo',
        'descripcion',
        'prioridad',
        'estado',
        'user_id',
        'asignado_user_id',
        'fecha_limite',
        'es_recurrente',
        'frecuencia',
        'fecha_inicio_recurrencia',
        'proxima_fecha_ejecucion',
    ];

    protected $casts = [
        'fecha_limite' => 'date',
        'es_recurrente' => 'boolean',
        'fecha_inicio_recurrencia' => 'date',
        'proxima_fecha_ejecucion' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function asignado()
    {
        return $this->belongsTo(User::class, 'asignado_user_id');
    }

    /**
     * Calcula la siguiente fecha de ejecución según la frecuencia.
     */
    public function calcularProximaFecha($desde = null)
    {
        if ($desde) {
            $fecha = \Carbon\Carbon::parse($desde);
        } elseif ($this->fecha_inicio_recurrencia) {
            $fecha = \Carbon\Carbon::parse($this->fecha_inicio_recurrencia);
        } else {
            $fecha = now();
        }

        switch (strtolower($this->frecuencia)) {
            case 'diario':
                return $fecha->addDay();
            case 'semanal':
                return $fecha->addWeek();
            case 'quincenal':
                return $fecha->addDays(15);
            case 'mensual':
                return $fecha->addMonth();
            case 'semestral':
                return $fecha->addMonths(6);
            case 'al año':
            case 'anual':
                return $fecha->addYear();
            default:
                return null;
        }
    }
}
