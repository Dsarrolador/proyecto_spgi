<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoRentabilidad extends Model
{
    protected $table = 'proyecto_rentabilidades';

    protected $fillable = [
        'proyecto_id',
        'fecha_analisis',
        'comision_porcentaje',
        'comision_user_id',
    ];

    protected $casts = [
        'fecha_analisis' => 'date',
        'comision_porcentaje' => 'decimal:2',
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }

    public function comisionUser()
    {
        return $this->belongsTo(User::class, 'comision_user_id');
    }

    public function proyecciones()
    {
        return $this->hasMany(ProyectoRentabilidadProyeccion::class, 'proyecto_rentabilidad_id');
    }

    public function gastos()
    {
        return $this->hasMany(ProyectoRentabilidadGasto::class, 'proyecto_rentabilidad_id');
    }

    public function horasExtras()
    {
        return $this->hasMany(ProyectoRentabilidadHoraExtra::class, 'proyecto_rentabilidad_id');
    }
}
