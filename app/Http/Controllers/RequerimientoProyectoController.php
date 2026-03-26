<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyecto;
use App\Models\ClienteMaestro;
use App\Models\TipoSoporte;
use App\Models\RequerimientoProyecto;

class RequerimientoProyectoController extends Controller
{
    public function index(Proyecto $proyecto)
    {
        $requerimientos = RequerimientoProyecto::with(['cliente', 'contacto', 'user'])
            ->deProyecto($proyecto->id)   // usa tu scope ✅
            ->latest()
            ->get();

        return view('proyectos.requerimientos.index', compact('proyecto', 'requerimientos'));
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
            'foto'              => 'nullable|image|max:5120',
            'estado_id'         => 'nullable|exists:estado_requerimientos,id',
        ]);

        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('requerimiento_proyecto', 'public');
        }

        RequerimientoProyecto::create([
            'id_proyecto'       => $proyecto->id,
            'cliente_id'        => $request->cliente_id ?: $proyecto->cliente_id,
            'contacto_id'       => $request->contacto_id ?: $proyecto->contacto_id,
            'tipo_soporte_id'   => $request->tipo_soporte_id ?: 1, // Fallback
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
            ->route('proyectos.requerimientos.index', $proyecto->id)
            ->with('success', 'Requerimiento del proyecto creado correctamente.');
    }
}