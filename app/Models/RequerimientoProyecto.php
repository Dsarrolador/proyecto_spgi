<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequerimientoProyecto extends Model
{
    protected $table = 'requerimiento_proyecto';

    public $timestamps = true;

    protected $fillable = [
        'id_proyecto',
        'cliente_id',
        'contacto_id',
        'tipo_soporte_id',
        'texto_imagen',
        'foto',
        'tiempo_transcurrido',
        'estado_id',
        'user_id',
        'asignado_user_id',
        'fecha_finalizado',
        'tiempo_invertido',
        'facturado',
        'archivo_factura',
        'es_recurrente',
        'frecuencia',
        'proxima_fecha_ejecucion',
        'fecha_inicio_recurrencia',
        'es_colaborativo',
        'prioridad',
    ];

    protected $casts = [
        'facturado'                => 'boolean',
        'es_recurrente'            => 'boolean',
        'es_colaborativo'          => 'boolean',
        'fecha_finalizado'         => 'datetime',
        'proxima_fecha_ejecucion' => 'datetime',
        'fecha_inicio_recurrencia' => 'datetime',
        'created_at'               => 'datetime',
        'updated_at'               => 'datetime',
        'prioridad'                => 'integer',
    ];

    /* ==========================
       RELACIONES
    ========================== */

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto', 'id');
    }

    public function cliente()
    {
        return $this->belongsTo(ClienteMaestro::class, 'cliente_id', 'id');
    }

    public function contacto()
    {
        return $this->belongsTo(LibretaContacto::class, 'contacto_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function asignado()
    {
        return $this->belongsTo(\App\Models\User::class, 'asignado_user_id', 'id');
    }

    public function estadoRequerimiento()
    {
        return $this->belongsTo(EstadoRequerimiento::class, 'estado_id', 'id');
    }

    public function tipoSoporte()
    {
        return $this->belongsTo(TipoSoporte::class, 'tipo_soporte_id', 'id');
    }

    public function novedades()
    {
        return $this->hasMany(\App\Models\NovedadRequerimientoProyecto::class, 'requerimiento_proyecto_id', 'id');
    }

    public function imagenes()
    {
        return $this->hasMany(\App\Models\RequerimientoProyectoImagen::class, 'requerimiento_proyecto_id', 'id');
    }

    public function colaboradores()
    {
        return $this->belongsToMany(\App\Models\User::class, 'requerimiento_proyecto_colaboradores', 'requerimiento_proyecto_id', 'user_id');
    }

    /* ==========================
       SCOPES
    ========================== */

    public function scopeDeProyecto($query, int $proyectoId)
    {
        return $query->where('id_proyecto', $proyectoId);
    }

    /* ==========================
       MUTATORS / ACCESSORS
    ========================== */

    public function getArchivosNovedadesAttribute()
    {
        $directory = 'novedades_proyectos/' . $this->id;
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
}