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
        'fecha_finalizado',
        'tiempo_invertido',
        'facturado',
    ];

    protected $casts = [
        'facturado'        => 'boolean',
        'fecha_finalizado' => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
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

    public function estadoRequerimiento()
    {
        return $this->belongsTo(EstadoRequerimiento::class, 'estado_id', 'id');
    }

    /* ==========================
       SCOPES
    ========================== */

    public function scopeDeProyecto($query, int $proyectoId)
    {
        return $query->where('id_proyecto', $proyectoId);
    }
}