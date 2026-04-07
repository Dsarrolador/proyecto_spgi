<?php

namespace App\Http\Controllers;

use App\Models\RequerimientoCliente;
use App\Models\ClienteMaestro;
use App\Models\TipoSoporte;
use App\Models\LibretaContacto;
use App\Models\User;
use App\Models\EstadoRequerimiento;
use App\Models\RequerimientoImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RequerimientoClienteController extends Controller
{
    private function esAdministracion(): bool
    {
        $u = Auth::user();
        if (!$u) {
            return false;
        }

        $roleName = null;

        if (method_exists($u, 'rol') && optional($u->rol)->nombre) {
            $roleName = $u->rol->nombre;
        } elseif (method_exists($u, 'role') && optional($u->role)->nombre) {
            $roleName = $u->role->nombre;
        } elseif (isset($u->role_id) && $u->role_id) {
            $role = \App\Models\Roles::find($u->role_id);
            $roleName = $role?->nombre;
        } elseif (isset($u->rol)) {
            $roleName = $u->rol;
        } elseif (isset($u->perfil)) {
            $roleName = $u->perfil;
        } elseif (isset($u->role_name)) {
            $roleName = $u->role_name;
        }

        if (!$roleName) {
            return false;
        }

        $norm = (string) Str::of($roleName)->ascii()->lower()->trim();

        return in_array($norm, [
            'administracion',
            'administrador',
            'admin',
            'administration',
        ], true);
    }

    private function col(string $column): bool
    {
        return Schema::hasColumn('requerimiento_cliente', $column);
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
        } else {
            if ($usuario) {
                $query->where('asignado_user_id', $usuario->id);
            }
        }

        if (!$estado) {
            $query->where('estado_id', '!=', 3);
        } elseif ($estado !== 'Todos') {
            $query->where('estado_id', (int) $estado);
        }

        if ($cliente_id) {
            $query->where('cliente_id', $cliente_id);
        }

        if ($this->col('facturado')) {
            if ($facturado !== null && $facturado !== '') {
                $query->where('facturado', (int) $facturado);
            }
        }

        if (!empty($categoria_iguala)) {
            $query->whereHas('clienteRelation', function ($q) use ($categoria_iguala) {
                $q->where('categoria_iguala', $categoria_iguala);
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
        $usuario = Auth::user();

        // 1. Obtener asignados para el combo del filtro
        $asignados = User::query()
            ->whereIn('id', function ($q) {
                $q->select('asignado_user_id')
                    ->from('requerimiento_cliente')
                    ->whereNotNull('asignado_user_id')
                    ->groupBy('asignado_user_id');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // 2. Query principal con filtros aplicados
        $query = RequerimientoCliente::with([
            'novedades.user',
            'clienteRelation',
            'user',
            'asignado',
        ]);

        $query = $this->applyFilters($request, $query);

        // 3. Clonar la query para las gráficas (sin paginación)
        $chartDataQuery = clone $query;

        // Gráfica de Clientes
        $chartClientes = (clone $chartDataQuery)
            ->selectRaw('count(*) as total, cliente_id')
            ->join('cliente_maestro', 'requerimiento_cliente.cliente_id', '=', 'cliente_maestro.id')
            ->selectRaw('cliente_maestro.nombre as label')
            ->groupBy('cliente_id', 'cliente_maestro.nombre')
            ->get();

        // Gráfica de Encargados
        $chartEncargados = (clone $chartDataQuery)
            ->selectRaw('count(*) as total, asignado_user_id')
            ->leftJoin('users', 'requerimiento_cliente.asignado_user_id', '=', 'users.id')
            ->selectRaw('COALESCE(users.name, "Sin asignar") as label')
            ->groupBy('asignado_user_id', 'users.name')
            ->get();

        return view('requerimientos.index', [
            'requerimientos'   => $query->orderByDesc('id')->paginate(15)->withQueryString(),
            'clientes'         => ClienteMaestro::orderBy('nombre')->get(),
            'asignados'        => $asignados,
            'estados'          => EstadoRequerimiento::all(),
            'asignado_id'      => $request->get('asignado_id', $request->get('asignado_user_id', 'mios')),
            'estado'           => $request->get('estado'),
            'cliente_id'       => $request->get('cliente_id'),
            'categoria_iguala' => $request->get('categoria_iguala'),
            'desde'            => $request->get('desde'),
            'hasta'            => $request->get('hasta'),
            'facturado'        => $request->get('facturado'),
            'chartClientes'    => $chartClientes,
            'chartEncargados'  => $chartEncargados,
            'esAdmin'          => $this->esAdministracion(),
            'esEncargado'      => $this->esRol('encargado'),
        ]);
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


   public function show($id)
{
    $requerimiento = RequerimientoCliente::with([
        'clienteRelation',
        'contactoRelation',
        'tipoSoporte',
        'user',
        'estadoRequerimiento',
        'imagenes',
    ])->findOrFail($id);

    $estados = EstadoRequerimiento::orderBy('nombre')->get();

    return view('requerimientos.show', compact('requerimiento', 'estados'));
}

    public function create()
    {
        $esAdmin = $this->esAdministracion();

        return view('requerimientos.create', [
            'clientes'     => ClienteMaestro::all(),
            'tiposSoporte' => TipoSoporte::where('activo', 1)->orderBy('nombre')->get(),
            'usuarios'     => User::orderBy('name')->get(['id', 'name', 'email']),
            'estados'      => EstadoRequerimiento::all(),
            'esAdmin'      => $esAdmin,
        ]);
    }

    public function store(Request $request)
    {
        $esAdmin = $this->esAdministracion();

        $request->validate([
            'cliente_id'       => 'required|exists:cliente_maestro,id',
            'contacto_id'      => 'nullable|exists:libreta_contacto,id',
            'tipo_soporte_id'  => 'required|exists:tipo_soporte,id',
            'texto_imagen'     => 'required|string|max:2000',
            'foto'             => 'nullable|image|max:30720',
            'imagenes'         => 'nullable|array',
            'imagenes.*'       => 'nullable|image|max:30720',
            'asignado_user_id' => 'nullable|exists:users,id',
        ]);

        $rutaFoto = null;

        if ($request->hasFile('foto')) {
            $rutaFoto = $request->file('foto')->store('Requerimientos', 'public');
        }

        $creadoPor  = Auth::id();
        $asignadoId = $request->filled('asignado_user_id')
            ? (int) $request->input('asignado_user_id')
            : ($esAdmin ? null : $creadoPor);

        $data = [
            'cliente_id'       => $request->cliente_id,
            'contacto_id'      => $request->contacto_id ?: null,
            'tipo_soporte_id'  => $request->tipo_soporte_id,
            'texto_imagen'     => $request->texto_imagen,
            'foto'             => $rutaFoto,
            'estado_id'        => 1,
            'user_id'          => $creadoPor,
            'creador_user_id'  => $creadoPor,
            'asignado_user_id' => $asignadoId,
        ];

        if ($this->col('facturado')) {
            $data['facturado'] = 0;
        }

        $requerimiento = RequerimientoCliente::create($data);

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                $ruta = $imagen->store('requerimientos', 'public');

                RequerimientoImagen::create([
                    'requerimiento_id' => $requerimiento->id,
                    'imagen'           => $ruta,
                ]);
            }
        }

        return redirect()->route('requerimientos.index')
            ->with('success', 'Requerimiento creado correctamente');
    }

    public function edit($id)
    {
        $requerimiento = RequerimientoCliente::with([
            'clienteRelation',
            'contactoRelation',
            'tipoSoporte',
            'user',
            'asignado',
            'imagenes',
            'estadoRequerimiento',
        ])->findOrFail($id);

        return view('requerimientos.edit', [
            'requerimiento' => $requerimiento,
            'clientes'      => ClienteMaestro::orderBy('nombre')->get(),
            'tiposSoporte'  => TipoSoporte::where('activo', 1)->orderBy('nombre')->get(),
            'contactos'     => LibretaContacto::orderBy('nombre')->get(),
            'estados'       => EstadoRequerimiento::all(),
            'esAdmin'       => $this->esAdministracion(),
            'usuarios'      => User::orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    public function update(Request $request, $id)
    {
        $req = RequerimientoCliente::findOrFail($id);
        $esAdmin = $this->esAdministracion();

        $rules = [
            'cliente_id'       => 'nullable|exists:cliente_maestro,id',
            'contacto_id'      => 'nullable|exists:libreta_contacto,id',
            'tipo_soporte_id'  => 'nullable|exists:tipo_soporte,id',
            'texto_imagen'     => 'nullable|string|max:2000',
            'estado_id'        => 'nullable|exists:estado_requerimientos,id',
            'foto'             => 'nullable|image|max:30720',
            'imagenes'         => 'nullable|array',
            'imagenes.*'       => 'nullable|image|max:30720',
            'created_at'       => 'nullable|date',
            'asignado_user_id' => 'nullable|exists:users,id',
        ];

        if ($this->col('fecha_finalizado')) {
            $rules['fecha_finalizado'] = 'nullable|date';
        }

        if ($this->col('tiempo_invertido')) {
            $rules['tiempo_invertido'] = 'nullable|string|max:20';
        }

        if ($this->col('facturado')) {
            $rules['facturado'] = 'nullable|in:0,1';
        }

        $request->validate($rules);

        if ($request->filled('cliente_id')) {
            $req->cliente_id = $request->cliente_id;
        }

        if ($request->has('contacto_id')) {
            $req->contacto_id = $request->contacto_id ?: null;
        }

        if ($request->filled('tipo_soporte_id')) {
            $req->tipo_soporte_id = $request->tipo_soporte_id;
        }

        if ($request->filled('texto_imagen')) {
            $req->texto_imagen = $request->texto_imagen;
        }

        if ($request->filled('estado_id')) {
            $req->estado_id = $request->estado_id;
        }

        if ($request->has('asignado_user_id')) {
            $req->asignado_user_id = $request->input('asignado_user_id') ?: null;
        }

        if ($request->hasFile('foto')) {
            $req->foto = $request->file('foto')->store('requerimientos', 'public');
        }

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                $ruta = $imagen->store('requerimientos', 'public');

                RequerimientoImagen::create([
                    'requerimiento_id' => $req->id,
                    'imagen'           => $ruta,
                ]);
            }
        }

        if ($request->filled('created_at')) {
            $req->created_at = Carbon::parse($request->created_at);
        }

        if ($this->col('fecha_finalizado') && $request->has('fecha_finalizado')) {
            $req->fecha_finalizado = $request->fecha_finalizado
                ? Carbon::parse($request->fecha_finalizado)
                : null;
        }

        if ($this->col('tiempo_invertido') && $request->has('tiempo_invertido')) {
            $req->tiempo_invertido = $request->tiempo_invertido ?: null;
        }

        if ($this->col('facturado') && $esAdmin && $request->has('facturado')) {
            $req->facturado = ((string) $request->input('facturado') === '1') ? 1 : 0;
        }

        if ($this->col('fecha_finalizado') && $req->estado_id == 3 && empty($req->fecha_finalizado)) {
            $req->fecha_finalizado = now();
        }

        $req->save();

        return redirect()->route('requerimientos.index')
            ->with('success', 'Requerimiento actualizado correctamente');
    }

    public function destroy($id)
    {
        $req = RequerimientoCliente::findOrFail($id);
        $req->delete();

        return redirect()->route('requerimientos.index')
            ->with('success', 'Requerimiento eliminado correctamente.');
    }
}