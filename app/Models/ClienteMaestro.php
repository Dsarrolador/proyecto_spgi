<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteMaestro extends Model
{
    protected $table = 'cliente_maestro';

    protected $fillable = [
        'nombre',
        'rnc',
        'telefono_principal',
        'clasificacion_negocio',
        'clasificacion_interna',
        'categoria_iguala',
        'categoria_iguala_id',
        'direccion_escrita',
        'notas',
    ];

    public function igualaPlan()
    {
        return $this->belongsTo(CategoriaIguala::class, 'categoria_iguala_id');
    }

    public function requerimientos()
    {
        return $this->hasMany(RequerimientoCliente::class, 'cliente_id');
    }

    /**
     * Cálculo de consumos del mes actual
     */
    public function getConsumoMesActual()
    {
        $inicioMes = now()->startOfMonth();
        $finMes = now()->endOfMonth();

        return $this->requerimientos()
            ->whereBetween('created_at', [$inicioMes, $finMes])
            ->where('estado_id', 3) // Solo se cuentan cuando se concluyen (Completado)
            ->with('tipoSoporte')
            ->get();
    }

    public function getMetricasIguala()
    {
        $plan = $this->igualaPlan;
        if (!$plan) return null;

        $consumos = $this->getConsumoMesActual();
        
        $usadosRemoto = 0;
        $usadosVisita = 0;

        foreach ($consumos as $req) {
            $clase = optional($req->tipoSoporte)->clase;
            if ($clase === 'remoto') $usadosRemoto++;
            elseif ($clase === 'visita') $usadosVisita++;
        }

        return (object)[
            'plan_nombre' => $plan->nombre,
            'limite_remoto' => $plan->cantidad_soporte_remoto,
            'limite_visita' => $plan->cantidad_visitas,
            'usados_remoto' => $usadosRemoto,
            'usados_visita' => $usadosVisita,
            'disponible_remoto' => max(0, $plan->cantidad_soporte_remoto - $usadosRemoto),
            'disponible_visita' => max(0, $plan->cantidad_visitas - $usadosVisita),
            'excedidos_remoto' => max(0, $usadosRemoto - $plan->cantidad_soporte_remoto),
            'excedidos_visita' => max(0, $usadosVisita - $plan->cantidad_visitas),
        ];
    }

    public function categoria()
    {
        // FK en cliente_maestro = clasificacion_interna
        // PK en categorias = id
        return $this->belongsTo(Categoria::class, 'clasificacion_interna', 'id');
    }

    public function contactos()
    {
        // (si ya la tienes, déjala como está)
        return $this->hasMany(LibretaContacto::class, 'codigo_cliente_maestro', 'id');
    }
}
