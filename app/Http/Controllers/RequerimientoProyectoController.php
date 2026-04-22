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
        $requerimientos = RequerimientoProyecto::with(['cliente', 'contacto', 'user'])
            ->deProyecto($proyecto->id)
            ->latest()
            ->paginate(15);

        return view('proyectos.show', compact('proyecto', 'requerimientos'));
    }

    public function create(Proyecto $proyecto)
    {
        $clientes = ClienteMaestro::orderBy('nombre')->get();
        $tiposSoporte = TipoSoporte::orderBy('nombre')->get();
        $estados = \App\Models\EstadoRequerimiento::all();

        return view('proyectos.requerimientos_create', compact('proyecto', 'clientes', 'tiposSoporte', 'estados'));
    }

    public function store(Request $request, Proyecto $proyecto)
    {
        $request->validate([
            'cliente_id'        => 'nullable|exists:cliente_maestro,id',
            'contacto_id'       => 'nullable|exists:libreta_contacto,id',
            'tipo_soporte_id'   => 'nullable|exists:tipo_soporte,id',
            'texto_imagen'      => 'required|string|max:2000',
            'foto'              => 'nullable|image|max:5242880',
            'estado_id'         => 'nullable|exists:estado_requerimientos,id',
        ]);

        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('RequerimientoProyecto', 'public');
        }

        RequerimientoProyecto::create([
            'id_proyecto'       => $proyecto->id,
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
            'facturado'         => 0,
        ]);

        return redirect()
            ->route('proyectos.show', $proyecto->id)
            ->with('success', 'Requerimiento del proyecto creado correctamente.');
    }

    public function show(RequerimientoProyecto $requerimientos_proyecto)
    {
        return view('proyectos.requerimientos_show', [
            'r' => $requerimientos_proyecto
        ]);
    }

    public function edit(RequerimientoProyecto $requerimientos_proyecto)
    {
        $proyecto = $requerimientos_proyecto->proyecto;
        $clientes = ClienteMaestro::orderBy('nombre')->get();
        $tiposSoporte = TipoSoporte::orderBy('nombre')->get();
        $estados = \App\Models\EstadoRequerimiento::all();

        return view('proyectos.requerimientos_edit', [
            'r' => $requerimientos_proyecto,
            'proyecto' => $proyecto,
            'clientes' => $clientes,
            'tiposSoporte' => $tiposSoporte,
            'estados' => $estados
        ]);
    }

    public function update(Request $request, RequerimientoProyecto $requerimientos_proyecto)
    {
        $request->validate([
            'cliente_id'      => 'nullable|exists:cliente_maestro,id',
            'contacto_id'     => 'nullable|exists:libreta_contacto,id',
            'tipo_soporte_id' => 'nullable|exists:tipo_soporte,id',
            'texto_imagen'    => 'required|string|max:2000',
            'foto'            => 'nullable|image|max:5242880',
            'estado_id'       => 'nullable|exists:estado_requerimientos,id',
        ]);

        $data = [
            'cliente_id'      => $request->cliente_id,
            'contacto_id'     => $request->contacto_id,
            'tipo_soporte_id' => $request->tipo_soporte_id,
            'texto_imagen'    => $request->texto_imagen,
            'estado_id'       => $request->estado_id,
        ];

        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($requerimientos_proyecto->foto) {
                Storage::disk('public')->delete($requerimientos_proyecto->foto);
            }
            $data['foto'] = $request->file('foto')->store('RequerimientoProyecto', 'public');
        }

        $requerimientos_proyecto->update($data);

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
}