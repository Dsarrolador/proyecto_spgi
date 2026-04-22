<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\ClienteMaestro;
use App\Models\RequerimientoProyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\NotificacionSistema;
use App\Models\User;

class ProyectoController extends Controller
{
    /* ==========================================================
     |  INDEX
     ========================================================== */
    public function index()
    {
        $clientes = ClienteMaestro::orderBy('nombre')->get();

        // Si realmente necesitas "usuarios" en la vista, déjalo así:
        $usuarios = class_exists(\App\Models\User::class)
            ? \App\Models\User::orderBy('name')->get()
            : collect();

        $query = Proyecto::query()->orderByDesc('id');

        // 🔎 Filtro por estado
        $estado = request('estado');
        if (!empty($estado) && $estado !== 'Todos') {
            $query->where('estado', $estado);
        }

        // 🔎 Filtro por cliente
        if (request('cliente_id')) {
            $query->where('cliente_id', request('cliente_id'));
        }

        $proyectos = $query
            ->with(['cliente', 'contacto'])
            ->paginate(15)
            ->withQueryString();

        return view('proyectos.index', compact('proyectos', 'clientes', 'usuarios'));
    }

    /* ==========================================================
     |  CREATE
     ========================================================== */
    public function create()
    {
        $clientes = ClienteMaestro::orderBy('nombre')->get();

        // ✅ Encargados (usuarios) para el select
        $encargados = class_exists(\App\Models\User::class)
            ? \App\Models\User::orderBy('name')->get()
            : collect();

        return view('proyectos.create', compact('clientes', 'encargados'));
    }

    /* ==========================================================
     |  STORE
     ========================================================== */
    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id'     => 'nullable|integer',
            'contacto_id'    => 'nullable|integer',
            'tipo_proyecto'  => 'required|string|max:80',
            'nombre'         => 'required|string|max:150',
            'alcance'        => 'nullable|string|max:2000',
            'fecha_inicio'   => 'nullable|date',
            'fecha_fin'      => 'nullable|date|after_or_equal:fecha_inicio',
            'prioridad'      => 'nullable|string|max:20',
            'estado'         => 'nullable|string|max:20',
            'adjunto'        => 'nullable|file|max:5242880|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
        ]);

        // ✅ Mapear alcance => descripcion (columna real)
        $data['descripcion'] = $data['alcance'] ?? null;
        unset($data['alcance']);

        // defaults
        $data['prioridad'] = $data['prioridad'] ?? 'Media';
        $data['estado']    = $data['estado'] ?? 'Activo';

        if ($request->hasFile('adjunto')) {
            $data['adjunto'] = $request->file('adjunto')->store('proyectos', 'public');
        }

        $proyectoNuevo = Proyecto::create($data);

        // NOTIFICACIÓN GLOBAL
        $usuarios = User::where('id', '!=', auth()->id())->get(['id']);
        $notificaciones = [];
        $sender_id = auth()->id();
        $sender_name = auth()->user()->name ?? 'Un usuario';
        
        foreach ($usuarios as $u) {
            $notificaciones[] = [
                'user_id' => $u->id,
                'sender_id' => $sender_id,
                'titulo' => 'Nuevo Proyecto Creado',
                'mensaje' => "{$sender_name} ha registrado un nuevo Proyecto: " . $proyectoNuevo->nombre,
                'url' => route('proyectos.index'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (count($notificaciones) > 0) {
            NotificacionSistema::insert($notificaciones);
        }

        return redirect()
            ->route('proyectos.index')
            ->with('success', 'Proyecto creado correctamente.');
    }

    /* ==========================================================
     |  SHOW (✅ AQUÍ ES DONDE FILTRAMOS REQUERIMIENTOS POR id_proyecto)
     ========================================================== */
    public function show(Proyecto $proyecto)
    {
        // ✅ Solo requerimientos de ESTE proyecto
        $requerimientos = RequerimientoProyecto::where('id_proyecto', $proyecto->id)
            ->orderByDesc('id')
            ->paginate(15);

        return view('proyectos.show', compact('proyecto', 'requerimientos'));
    }

    /* ==========================================================
     |  EDIT
     ========================================================== */
    public function edit(Proyecto $proyecto)
    {
        $clientes = ClienteMaestro::orderBy('nombre')->get();

        // ✅ Encargados (usuarios) por si lo usas en el edit
        $encargados = class_exists(\App\Models\User::class)
            ? \App\Models\User::orderBy('name')->get()
            : collect();

        // Si tu edit usa "alcance" en textarea, pásalo desde descripcion
        $proyecto->alcance = $proyecto->descripcion;

        return view('proyectos.edit', compact('proyecto', 'clientes', 'encargados'));
    }

    /* ==========================================================
     |  UPDATE
     ========================================================== */
    public function update(Request $request, Proyecto $proyecto)
    {
        $data = $request->validate([
            'cliente_id'     => 'required|integer',
            'contacto_id'    => 'nullable|integer',
            'tipo_proyecto'  => 'required|string|max:80',
            'nombre'         => 'required|string|max:150',
            'alcance'        => 'nullable|string|max:2000',
            'fecha_inicio'   => 'nullable|date',
            'fecha_fin'      => 'nullable|date|after_or_equal:fecha_inicio',
            'prioridad'      => 'nullable|string|max:20',
            'estado'         => 'nullable|string|max:20',
            'adjunto'        => 'nullable|file|max:5242880|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
        ]);

        // ✅ Mapear alcance => descripcion
        $data['descripcion'] = $data['alcance'] ?? null;
        unset($data['alcance']);

        if ($request->hasFile('adjunto')) {
            if ($proyecto->adjunto) {
                Storage::disk('public')->delete($proyecto->adjunto);
            }
            $data['adjunto'] = $request->file('adjunto')->store('proyectos', 'public');
        }

        $proyecto->update($data);

        return redirect()
            ->route('proyectos.index')
            ->with('success', 'Proyecto actualizado correctamente.');
    }

    /* ==========================================================
     |  DESTROY
     ========================================================== */
    public function destroy(Proyecto $proyecto)
    {
        if (!empty($proyecto->adjunto)) {
            Storage::disk('public')->delete($proyecto->adjunto);
        }

        $proyecto->delete();

        return redirect()
            ->route('proyectos.index')
            ->with('success', 'Proyecto eliminado correctamente.');
    }

    public function download(Proyecto $proyecto)
    {
        if (!$proyecto->adjunto) {
            return back()->with('error', 'Este proyecto no tiene adjunto.');
        }

        if (Storage::disk('public')->exists($proyecto->adjunto)) {
            return Storage::disk('public')->download($proyecto->adjunto, basename($proyecto->adjunto));
        }

        return back()->with('error', 'El archivo no existe en el servidor externo.');
    }
}