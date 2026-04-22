<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequerimientoCliente extends Model
{
    use HasFactory;

    protected $table = 'requerimiento_cliente';

    protected $fillable = [
        'cliente_id',
        'contacto_id',
        'tipo_soporte_id',
        'texto_imagen',
        'foto',
        'estado_id',
        'tiempo_transcurrido',
        'user_id',              // ✅ CREADO POR
        'asignado_user_id',     // ✅ ASIGNADO A
        'fecha_finalizado',
        'tiempo_invertido',
        'facturado',
        'archivo_factura',
        'es_recurrente',
        'frecuencia',
        'proxima_fecha_ejecucion',
        'fecha_inicio_recurrencia',
        'es_colaborativo',
    ];

    protected $casts = [
        'facturado' => 'boolean',
        'fecha_finalizado' => 'datetime',
        'proxima_fecha_ejecucion' => 'datetime',
        'fecha_inicio_recurrencia' => 'datetime',
        'es_recurrente' => 'boolean',
        'es_colaborativo' => 'boolean',
    ];

    public function clienteRelation()
    {
        return $this->belongsTo(\App\Models\ClienteMaestro::class, 'cliente_id', 'id');
    }

    public function contacto()
    {
        return $this->belongsTo(\App\Models\LibretaContacto::class, 'contacto_id', 'id');
    }

    public function contactoRelation()
    {
        return $this->contacto();
    }

    public function tipoSoporte()
    {
        return $this->belongsTo(\App\Models\TipoSoporte::class, 'tipo_soporte_id', 'id');
    }

    public function novedades()
    {
        return $this->hasMany(\App\Models\NovedadRequerimiento::class, 'requerimiento_id', 'id');
    }

    // ✅ CREADO POR
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    // ✅ ASIGNADO A
    public function asignado()
    {
        return $this->belongsTo(\App\Models\User::class, 'asignado_user_id', 'id');
    }

    public function imagenes()
    {
        return $this->hasMany(RequerimientoImagen::class,'requerimiento_id');
    }

    public function colaboradores()
    {
        return $this->belongsToMany(\App\Models\User::class, 'requerimiento_colaboradores', 'requerimiento_id', 'user_id');
    }

    public function estadoRequerimiento()
    {
        return $this->belongsTo(EstadoRequerimiento::class, 'estado_id', 'id');
    }

    /**
     * ✅ Obtiene archivos desde la carpeta del requerimiento
     */
    public function getArchivosNovedadesAttribute()
    {
        $directory = 'novedades/' . $this->id;
        $files = [];
        
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($directory)) {
            $allFiles = \Illuminate\Support\Facades\Storage::disk('public')->files($directory);
            foreach ($allFiles as $file) {
                $files[] = (object) [
                    'nombre' => basename($file),
                    'url'    => asset('storage/' . $file),
                    'fecha'  => \Carbon\Carbon::createFromTimestamp(\Illuminate\Support\Facades\Storage::disk('public')->lastModified($file)),
                ];
            }
        }
        
        return collect($files);
    }

    /**
     * Calcula la siguiente fecha de ejecución según la frecuencia
     */
    public function calcularProximaFecha($desde = null)
    {
        // Prioridad: 1. Desde (pasado por argumento), 2. Fecha Inicio Recurrencia, 3. Now
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