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

        $limiteRemoto = $plan->cantidad_soporte_remoto;
        $limiteVisita = $plan->cantidad_visitas;

        return (object)[
            'plan_nombre' => $plan->nombre,
            'limite_remoto' => $limiteRemoto,
            'limite_visita' => $limiteVisita,
            'usados_remoto' => $usadosRemoto,
            'usados_visita' => $usadosVisita,
            'disponible_remoto' => ($limiteRemoto == -1) ? 999 : max(0, $limiteRemoto - $usadosRemoto),
            'disponible_visita' => ($limiteVisita == -1) ? 999 : max(0, $limiteVisita - $usadosVisita),
            'excedidos_remoto' => ($limiteRemoto == -1) ? 0 : max(0, $usadosRemoto - $limiteRemoto),
            'excedidos_visita' => ($limiteVisita == -1) ? 0 : max(0, $usadosVisita - $limiteVisita),
            'es_ilimitado_remoto' => ($limiteRemoto == -1),
            'es_ilimitado_visita' => ($limiteVisita == -1),
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
        return $this->hasMany(LibretaContacto::class, 'codigo_cliente_maestro', 'id');
    }

    public function entornoAnydesks()
    {
        return $this->hasMany(ClienteAnydesk::class, 'cliente_id');
    }

    public function entornoDocumentos()
    {
        return $this->hasMany(ClienteEntornoDocumento::class, 'cliente_id');
    }

    public function entornoBitacoras()
    {
        return $this->hasMany(ClienteBitacora::class, 'cliente_id');
    }

    public function entornoEquipos()
    {
        return $this->hasMany(ClienteEquipo::class, 'cliente_id');
    }
}
