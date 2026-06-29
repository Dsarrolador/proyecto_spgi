<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Proyecto;
use App\Models\ClienteMaestro;
use App\Models\TipoSoporte;
use App\Models\RequerimientoProyecto;

class RequerimientoProyectoController extends Controller
{
    public function index(Proyecto $proyecto)
    {
        $query = RequerimientoProyecto::with(['cliente', 'contacto', 'user', 'estadoRequerimiento', 'colaboradores', 'tareas', 'requerimientoCliente'])
            ->deProyecto($proyecto->id);

        if (request('estado')) {
            $query->where('estado_id', request('estado'));
        }

        if (request('asignado_id') && request('asignado_id') !== 'todos') {
            if (request('asignado_id') == 'mios') {
                $query->where('asignado_user_id', auth()->id());
            } else {
                $query->where('asignado_user_id', request('asignado_id'));
            }
        }

        if (request()->has('facturado') && request('facturado') != '') {
            $query->where('facturado', request('facturado'));
        }

        if (request('desde')) {
            $query->whereDate('created_at', '>=', request('desde'));
        }

        if (request('hasta')) {
            $query->whereDate('created_at', '<=', request('hasta'));
        }

        if (request('prioridad')) {
            $query->where('prioridad', request('prioridad'));
        }

        $requerimientos = $query->orderByDesc('prioridad')->latest()->paginate(15)->withQueryString();
        
        $usuarios = \App\Models\User::orderBy('name')->get(['id', 'name', 'email']);
        $estados = \App\Models\EstadoRequerimiento::all();

        return view('proyectos.show', compact('proyecto', 'requerimientos', 'usuarios', 'estados'));
    }

    public function create(Request $request, Proyecto $proyecto)
    {
        $clientes = ClienteMaestro::orderBy('nombre')->get();
        $tiposSoporte = TipoSoporte::orderBy('nombre')->get();
        $estados = \App\Models\EstadoRequerimiento::all();
        $usuarios = \App\Models\User::orderBy('name')->get();
        
        $tareas = \App\Models\RequerimientoCliente::where('proyecto_id', $proyecto->id)->get();
        $selected_tarea_id = $request->query('requerimiento_cliente_id');

        return view('proyectos.requerimientos_create', compact('proyecto', 'clientes', 'tiposSoporte', 'estados', 'usuarios', 'tareas', 'selected_tarea_id'));
    }

    public function store(Request $request, Proyecto $proyecto)
    {
        $request->validate([
            'cliente_id'        => 'nullable|exists:cliente_maestro,id',
            'requerimiento_cliente_id' => 'nullable|exists:requerimiento_cliente,id',
            'contacto_id'       => 'nullable|exists:libreta_contacto,id',
            'tipo_soporte_id'   => 'nullable|exists:tipo_soporte,id',
            'texto_imagen'      => 'required|string|max:2000',
            'foto'              => 'nullable|image|max:5242880',
            'estado_id'         => 'nullable|exists:estado_requerimientos,id',
            'parent_id'         => 'nullable|exists:requerimiento_proyecto,id',
        ]);

        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('RequerimientoProyecto', 'public');
        }

        $req = RequerimientoProyecto::create([
            'id_proyecto'       => $proyecto->id,
            'requerimiento_cliente_id' => $request->requerimiento_cliente_id ?: null,
            'cliente_id'        => $request->cliente_id ?: $proyecto->cliente_id,
            'contacto_id'       => $request->contacto_id ?: $proyecto->contacto_id,
            'tipo_soporte_id'   => $request->tipo_soporte_id ?: 1,
            'texto_imagen'      => $request->texto_imagen,
            'foto'              => $path,
            'estado_id'         => $request->estado_id ?: 1,
            'user_id'           => auth()->id(),
            'tiempo_transcurrido' => null,
            'fecha_finalizado'  => null,
            'tiempo_invertido'  => null,
            'facturado'         => $request->has('facturado') ? $request->facturado : 0,
            'prioridad'         => $request->filled('prioridad') ? $request->prioridad : 3,
            'asignado_user_id'  => $request->filled('asignado_user_id') ? $request->asignado_user_id : null,
            'es_recurrente'     => $request->has('es_recurrente'),
            'frecuencia'        => $request->frecuencia,
            'fecha_inicio_recurrencia' => $request->fecha_inicio_recurrencia,
            'es_colaborativo'   => $request->has('es_colaborativo'),
        ]);

        if ($req->es_colaborativo && $request->filled('colaboradores_ids')) {
            $req->colaboradores()->sync($request->colaboradores_ids);
        }

        return redirect()
            ->route('proyectos.show', $proyecto->id)
            ->with('success', 'Requerimiento del proyecto creado correctamente.');
    }

    public function show(RequerimientoProyecto $requerimientos_proyecto)
    {
        $estados = \App\Models\EstadoRequerimiento::all();
        
        return view('proyectos.requerimientos_show', [
            'r' => $requerimientos_proyecto,
            'estados' => $estados
        ]);
    }

    public function edit(RequerimientoProyecto $requerimientos_proyecto)
    {
        $proyecto = $requerimientos_proyecto->proyecto;
        $clientes = ClienteMaestro::orderBy('nombre')->get();
        $tiposSoporte = TipoSoporte::orderBy('nombre')->get();
        $estados = \App\Models\EstadoRequerimiento::all();
        $usuarios = \App\Models\User::orderBy('name')->get();
        
        $tareas = \App\Models\RequerimientoCliente::where('proyecto_id', $proyecto->id)->get();

        return view('proyectos.requerimientos_edit', [
            'r' => $requerimientos_proyecto,
            'proyecto' => $proyecto,
            'clientes' => $clientes,
            'tiposSoporte' => $tiposSoporte,
            'estados' => $estados,
            'usuarios' => $usuarios,
            'tareas' => $tareas,
        ]);
    }

    public function update(Request $request, RequerimientoProyecto $requerimientos_proyecto)
    {
        $request->validate([
            'cliente_id'      => 'nullable|exists:cliente_maestro,id',
            'requerimiento_cliente_id' => 'nullable|exists:requerimiento_cliente,id',
            'contacto_id'     => 'nullable|exists:libreta_contacto,id',
            'tipo_soporte_id' => 'nullable|exists:tipo_soporte,id',
            'texto_imagen'    => 'required|string|max:2000',
            'foto'            => 'nullable|image|max:5242880',
            'estado_id'       => 'nullable|exists:estado_requerimientos,id',
        ]);

        $data = [
            'cliente_id'      => $request->cliente_id,
            'requerimiento_cliente_id' => $request->requerimiento_cliente_id ?: null,
            'contacto_id'     => $request->contacto_id,
            'tipo_soporte_id' => $request->tipo_soporte_id,
            'texto_imagen'    => $request->texto_imagen,
            'estado_id'       => $request->estado_id,
            'prioridad'       => $request->filled('prioridad') ? $request->prioridad : 3,
            'asignado_user_id'=> $request->filled('asignado_user_id') ? $request->asignado_user_id : null,
            'es_recurrente'   => $request->has('es_recurrente'),
            'frecuencia'      => $request->frecuencia,
            'fecha_inicio_recurrencia' => $request->fecha_inicio_recurrencia,
            'es_colaborativo' => $request->has('es_colaborativo'),
        ];

        if ($request->has('facturado')) {
            $data['facturado'] = $request->facturado;
        }

        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($requerimientos_proyecto->foto) {
                Storage::disk('public')->delete($requerimientos_proyecto->foto);
            }
            $data['foto'] = $request->file('foto')->store('RequerimientoProyecto', 'public');
        }

        $requerimientos_proyecto->update($data);

        if ($request->has('es_colaborativo') && $request->filled('colaboradores_ids')) {
            $requerimientos_proyecto->colaboradores()->sync($request->colaboradores_ids);
        } elseif (!$request->has('es_colaborativo')) {
            $requerimientos_proyecto->colaboradores()->detach();
        }

        return redirect()
            ->route('proyectos.show', $requerimientos_proyecto->id_proyecto)
            ->with('success', 'Requerimiento del proyecto actualizado correctamente.');
    }

    public function destroy(RequerimientoProyecto $requerimientos_proyecto)
    {
        $id_proyecto = $requerimientos_proyecto->id_proyecto;

        if ($requerimientos_proyecto->foto) {
            Storage::disk('public')->delete($requerimientos_proyecto->foto);
        }

        $requerimientos_proyecto->delete();

        return redirect()
            ->route('proyectos.show', $id_proyecto)
            ->with('success', 'Requerimiento eliminado correctamente.');
    }

    public function getNotes(RequerimientoProyecto $requerimientos_proyecto)
    {
        if ($requerimientos_proyecto->notas_last_user_id && $requerimientos_proyecto->notas_last_user_id !== auth()->id() && !$requerimientos_proyecto->notas_seen) {
            $requerimientos_proyecto->update(['notas_seen' => true]);
        }

        return response()->json([
            'success' => true,
            'notas_internas' => $requerimientos_proyecto->notas_internas ?? '',
            'notas_clientes' => $requerimientos_proyecto->notas_clientes ?? '',
            'last_editor' => $requerimientos_proyecto->notasLastUser ? $requerimientos_proyecto->notasLastUser->name : null,
            'updated_at' => $requerimientos_proyecto->updated_at ? $requerimientos_proyecto->updated_at->format('d/m/Y H:i') : null,
        ]);
    }

    public function saveNotes(Request $request, RequerimientoProyecto $requerimientos_proyecto)
    {
        $request->validate([
            'tipo' => 'required|in:interno,cliente',
            'nota' => 'nullable|string|max:10000',
        ]);

        $column = $request->tipo === 'interno' ? 'notas_internas' : 'notas_clientes';

        $requerimientos_proyecto->update([
            $column => $request->nota,
            'notas_last_user_id' => auth()->id(),
            'notas_seen' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notas guardadas correctamente.',
            'last_editor' => auth()->user()->name,
            'updated_at' => now()->format('d/m/Y H:i'),
        ]);
    }

    public function storeTarea(Request $request, RequerimientoProyecto $requerimientos_proyecto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $tarea = $requerimientos_proyecto->tareas()->create([
            'nombre' => $request->nombre,
            'completada' => false,
        ]);

        return response()->json([
            'success' => true,
            'tarea' => $tarea
        ]);
    }

    public function toggleTarea(Request $request, $id)
    {
        $tarea = \App\Models\RequerimientoProyectoTarea::findOrFail($id);
        $tarea->update([
            'completada' => !$tarea->completada
        ]);

        return response()->json([
            'success' => true,
            'tarea' => $tarea
        ]);
    }

    public function destroyTarea($id)
    {
        $tarea = \App\Models\RequerimientoProyectoTarea::findOrFail($id);
        $tarea->delete();

        return response()->json([
            'success' => true
        ]);
    }
}