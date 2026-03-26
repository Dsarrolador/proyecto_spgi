<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'proyectos';

    /**
     * Campos permitidos para mass assignment.
     */
    protected $fillable = [
        'cliente_id',
        'contacto_id',
        'tipo_proyecto',
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'prioridad',
        'estado',
        'adjunto',
        'activo',
    ];

    /**
     * Casts.
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'activo'       => 'boolean',
    ];

    /* ==========================================================
     |  RELACIONES PRINCIPALES
     ========================================================== */

    /**
     * Relación con Cliente.
     */
    public function cliente()
    {
        return $this->belongsTo(ClienteMaestro::class, 'cliente_id', 'id');
    }

    /**
     * Alias para compatibilidad con vistas existentes: $p->clienteRelation
     */
    public function clienteRelation()
    {
        return $this->cliente();
    }

    /**
     * Relación con Contacto.
     */
    public function contacto()
    {
        return $this->belongsTo(LibretaContacto::class, 'contacto_id', 'id');
    }

    /**
     * Alias para compatibilidad con vistas existentes: $p->contactoRelation
     */
    public function contactoRelation()
    {
        return $this->contacto();
    }

    /**
     * Encargado del proyecto (si tu tabla tiene user_id).
     * Si NO tienes esta columna, simplemente no uses esta relación en las vistas.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    /**
     * (Opcional) Si en vez de user_id tú usas creado_por.
     * Úsala solo si esa columna existe y la vas a consumir.
     */
    public function creadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'creado_por', 'id');
    }

    /* ==========================================================
     |  REQUERIMIENTOS DEL PROYECTO
     |  FK: requerimiento_proyecto.id_proyecto -> proyectos.id
     ========================================================== */

    /**
     * Requerimientos asociados a este proyecto.
     * Esto es lo que te permitirá filtrar automáticamente al entrar a un proyecto.
     */
    public function requerimientos()
    {
        return $this->hasMany(RequerimientoProyecto::class, 'id_proyecto', 'id');
    }

    /* ==========================================================
     |  SCOPES (OPCIONAL)
     ========================================================== */

    /**
     * Scope para listar solo proyectos activos.
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }
}