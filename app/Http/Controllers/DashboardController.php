<?php

namespace App\Http\Controllers;

use App\Models\RequerimientoCliente;
use App\Models\ClienteMaestro;
use App\Models\User;
use App\Models\EstadoRequerimiento;
use App\Models\CategoriaIguala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private function esAdministracion(): bool
    {
        $u = Auth::user();
        if (!$u) return false;

        $roleName = null;
        if (method_exists($u, 'rol') && optional($u->rol)->nombre) {
            $roleName = $u->rol->nombre;
        } elseif (method_exists($u, 'role') && optional($u->role)->nombre) {
            $roleName = $u->role->nombre;
        } elseif (isset($u->role_id) && $u->role_id) {
            $role = \App\Models\Roles::find($u->role_id);
            $roleName = $role?->nombre;
        } elseif (isset($u->cod_roleUser)) {
           $role = \App\Models\RoleUser::find($u->cod_roleUser);
           $roleName = $role?->nombre;
        }

        if (!$roleName) return false;

        $norm = (string) Str::of($roleName)->ascii()->lower()->trim();
        return in_array($norm, ['administracion', 'administrador', 'admin', 'administration'], true);
    }

    private function esRol(string $roleName): bool
    {
        $u = Auth::user();
        if (!$u) return false;

        $rNm = null;
        if (method_exists($u, 'rol') && optional($u->rol)->nombre) {
            $rNm = $u->rol->nombre;
        } elseif (method_exists($u, 'role') && optional($u->role)->nombre) {
            $rNm = $u->role->nombre;
        } elseif (isset($u->role_id) && $u->role_id) {
            $role = \App\Models\Roles::find($u->role_id);
            $rNm = $role?->nombre;
        } elseif (isset($u->cod_roleUser)) {
           $role = \App\Models\RoleUser::find($u->cod_roleUser);
           $rNm = $role?->nombre;
        }

        if (!$rNm) return false;

        $norm = (string) Str::of($rNm)->ascii()->lower()->trim();
        return $norm === (string) Str::of($roleName)->ascii()->lower()->trim();
    }

    private function applyFilters(Request $request, $query)
    {
        $usuario = Auth::user();

        $estado           = $request->get('estado');
        $cliente_id       = $request->get('cliente_id');
        $categoria_iguala = $request->get('categoria_iguala');
        $desde            = $request->get('desde');
        $hasta            = $request->get('hasta');
        $facturado        = $request->get('facturado');
        $asignado_id      = $request->get('asignado_id', $request->get('asignado_user_id', 'mios'));

        if ($asignado_id === 'mios' || $asignado_id === null || $asignado_id === '') {
            if ($usuario) {
                $query->where('asignado_user_id', $usuario->id);
            }
        } elseif ($asignado_id === 'todos') {
            // sin filtro
        } elseif (is_numeric($asignado_id)) {
            $query->where('asignado_user_id', (int) $asignado_id);
        }

        if (!$estado) {
            $query->where('estado_id', '!=', 3);
        } elseif ($estado !== 'Todos') {
            $query->where('estado_id', (int) $estado);
        }

        if ($cliente_id) {
            $query->where('cliente_id', $cliente_id);
        }

        if (Schema::hasColumn('requerimiento_cliente', 'facturado')) {
            if ($facturado !== null && $facturado !== '') {
                $query->where('facturado', (int) $facturado);
            }
        }

        if (!empty($categoria_iguala)) {
            $query->whereHas('clienteRelation', function ($q) use ($categoria_iguala) {
                if (is_numeric($categoria_iguala)) {
                    $q->where('categoria_iguala_id', (int) $categoria_iguala);
                } else {
                    $q->where('categoria_iguala', $categoria_iguala);
                }
            });
        }

        if (!empty($desde) && !empty($hasta)) {
            $query->whereBetween('requerimiento_cliente.created_at', [
                Carbon::parse($desde)->startOfDay(),
                Carbon::parse($hasta)->endOfDay(),
            ]);
        } elseif (!empty($desde)) {
            $query->where('requerimiento_cliente.created_at', '>=', Carbon::parse($desde)->startOfDay());
        } elseif (!empty($hasta)) {
            $query->where('requerimiento_cliente.created_at', '<=', Carbon::parse($hasta)->endOfDay());
        }

        return $query;
    }

    public function index(Request $request)
    {
        if (!Auth::user()->es_admin && !Auth::user()->es_encargado) {
            return redirect()->route('bienvenido')->with('error', 'Acceso restringido al Dashboard de Gráficas.');
        }

        $usuario = Auth::user();

        $asignados = User::query()
            ->whereIn('id', function ($q) {
                $q->select('asignado_user_id')
                    ->from('requerimiento_cliente')
                    ->whereNotNull('asignado_user_id')
                    ->groupBy('asignado_user_id');
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        $baseQuery = RequerimientoCliente::query();
        $this->applyFilters($request, $baseQuery);

        // 1. Requerimientos por Cliente
        $chartClientes = (clone $baseQuery)
            ->selectRaw('count(*) as total, cliente_maestro.nombre as label, cliente_id as id')
            ->join('cliente_maestro', 'requerimiento_cliente.cliente_id', '=', 'cliente_maestro.id')
            ->groupBy('cliente_maestro.nombre', 'cliente_id')
            ->get();

        // 2. Requerimientos por Encargado
        $chartEncargados = (clone $baseQuery)
            ->selectRaw('count(*) as total, COALESCE(users.name, "Sin asignar") as label, asignado_user_id as id')
            ->leftJoin('users', 'requerimiento_cliente.asignado_user_id', '=', 'users.id')
            ->groupBy('users.name', 'asignado_user_id')
            ->get();

        // 3. Estados por Encargado
        $chartEstadoPorEncargado = (clone $baseQuery)
            ->selectRaw('count(*) as total, estado_requerimientos.nombre as estado, estado_id, COALESCE(users.name, "Sin asignar") as encargado, asignado_user_id')
            ->join('estado_requerimientos', 'requerimiento_cliente.estado_id', '=', 'estado_requerimientos.id')
            ->leftJoin('users', 'requerimiento_cliente.asignado_user_id', '=', 'users.id')
            ->groupBy('estado_requerimientos.nombre', 'estado_id', 'users.name', 'asignado_user_id')
            ->get();

        // 4. Estados por Cliente
        $chartEstadoPorCliente = (clone $baseQuery)
            ->selectRaw('count(*) as total, estado_requerimientos.nombre as estado, estado_id, cliente_maestro.nombre as cliente, cliente_id')
            ->join('estado_requerimientos', 'requerimiento_cliente.estado_id', '=', 'estado_requerimientos.id')
            ->join('cliente_maestro', 'requerimiento_cliente.cliente_id', '=', 'cliente_maestro.id')
            ->groupBy('estado_requerimientos.nombre', 'estado_id', 'cliente_maestro.nombre', 'cliente_id')
            ->get();

        // 5. Resumen estructurado por Cliente para la tabla inferior
        $clientSummary = (clone $baseQuery)
            ->selectRaw('count(*) as total, cliente_id, cliente_maestro.nombre as cliente_nombre')
            ->join('cliente_maestro', 'requerimiento_cliente.cliente_id', '=', 'cliente_maestro.id')
            ->groupBy('cliente_id', 'cliente_maestro.nombre')
            ->orderBy('cliente_maestro.nombre')
            ->get();

        foreach ($clientSummary as $client) {
            $client->states = (clone $baseQuery)
                ->where('cliente_id', $client->cliente_id)
                ->selectRaw('count(*) as total, estado_id')
                ->groupBy('estado_id')
                ->pluck('total', 'estado_id')
                ->toArray();
        }

        // 6. Totales generales para la vista "General"
        $generalTotals = [
            'total' => (clone $baseQuery)->count(),
            'states' => (clone $baseQuery)
                ->selectRaw('count(*) as total, estado_id')
                ->groupBy('estado_id')
                ->pluck('total', 'estado_id')
                ->toArray()
        ];

        $estados = EstadoRequerimiento::all();

        return view('dashboard.index', [
            'chartClientes'           => $chartClientes,
            'chartEncargados'         => $chartEncargados,
            'chartEstadoPorEncargado' => $chartEstadoPorEncargado,
            'chartEstadoPorCliente'   => $chartEstadoPorCliente,
            'clientSummary'           => $clientSummary,
            'generalTotals'           => $generalTotals, // Enviamos los totales generales
            'clientes'                => ClienteMaestro::orderBy('nombre')->get(),
            'asignados'               => $asignados,
            'estadosList'             => $estados,
            'categoriasIguala'        => CategoriaIguala::orderBy('nombre')->get(),
            'esAdmin'                 => $this->esAdministracion(),
            'esEncargado'             => $this->esRol('encargado'),
        ]);
    }

    public function igualaControl(Request $request)
    {
        if (!Auth::user()->esAdmin && !Auth::user()->esEncargado) {
            return redirect()->route('bienvenido')->with('error', 'Acceso restringido al Control de Igualas.');
        }

        $clientes = ClienteMaestro::whereNotNull('categoria_iguala_id')
            ->whereHas('igualaPlan', function($q) {
                $q->where('nombre', '!=', 'Cliente sin iguala');
            })
            ->with('igualaPlan')
            ->orderBy('nombre')
            ->get();

        $data = $clientes->map(function ($cliente) {
            return [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'metrics' => $cliente->getMetricasIguala()
            ];
        })->filter();

        return view('dashboard.iguala_control', [
            'data' => $data,
            'esAdmin' => $this->esAdministracion(),
            'esEncargado' => $this->esRol('encargado'),
        ]);
    }

    public function getClienteMetrics($id)
    {
        $cliente = ClienteMaestro::find($id);
        if (!$cliente) return response()->json(['error' => 'Cliente no encontrado'], 404);

        $metrics = $cliente->getMetricasIguala();
        return response()->json($metrics);
    }
}
