<?php

namespace App\Http\Controllers;

use App\Models\HoraExtra;
use App\Models\HoraExtraDetalle;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class HoraExtraController extends Controller
{
    public function index()
    {
        $planillas = HoraExtra::with(['user', 'responsable'])
            ->orderBy('fecha_registro', 'desc')
            ->get();

        return view('administracion.horas_extras.index', compact('planillas'));
    }

    public function create()
    {
        return view('administracion.horas_extras.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_registro' => 'required|date',
        ]);

        $planilla = HoraExtra::create([
            'titulo' => $request->titulo,
            'fecha_registro' => $request->fecha_registro,
            'estado' => 'Borrador',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('horas-extras.show', $planilla->id)
            ->with('success', 'Planilla creada. Ahora puedes agregar el detalle de las horas extras.');
    }

    public function show($id)
    {
        $planilla = HoraExtra::with(['user', 'responsable', 'detalles' => function($q) {
            $q->orderBy('fecha', 'asc');
        }])->findOrFail($id);

        $usuarios = \App\Models\User::orderBy('name')->get();

        return view('administracion.horas_extras.show', compact('planilla', 'usuarios'));
    }

    public function storeDetalle(Request $request, $id)
    {
        $request->validate([
            'fecha' => 'required|date',
            'colaborador' => 'required|string|max:255',
            'concepto' => 'required|string|max:255',
            'hora_inicio' => 'required',
            'hora_salida' => 'required',
            'total_horas' => 'required|numeric|min:0.01',
        ]);

        $planilla = HoraExtra::findOrFail($id);

        if ($planilla->estado === 'Aprobado') {
            return redirect()->back()->with('error', 'No se pueden agregar detalles a una planilla aprobada.');
        }

        HoraExtraDetalle::create([
            'hora_extra_id' => $planilla->id,
            'fecha' => $request->fecha,
            'colaborador' => $request->colaborador,
            'concepto' => $request->concepto,
            'hora_inicio' => $request->hora_inicio,
            'hora_salida' => $request->hora_salida,
            'total_horas' => $request->total_horas,
            'tarifa_hora' => 0.00,
        ]);

        return redirect()->route('horas-extras.show', $id)
            ->with('success', 'Registro de hora extra agregado correctamente.');
    }

    public function deleteDetalle($id, $detalle_id)
    {
        $planilla = HoraExtra::findOrFail($id);

        if ($planilla->estado === 'Aprobado') {
            return redirect()->back()->with('error', 'No se pueden eliminar detalles de una planilla aprobada.');
        }

        $detalle = HoraExtraDetalle::where('hora_extra_id', $id)->findOrFail($detalle_id);
        $detalle->delete();

        return redirect()->route('horas-extras.show', $id)
            ->with('success', 'Registro eliminado correctamente.');
    }

    public function updateGeneral(Request $request, $id)
    {
        $request->validate([
            'observaciones' => 'nullable|string',
            'responsable_id' => 'nullable|exists:users,id',
        ]);

        $planilla = HoraExtra::findOrFail($id);

        if ($planilla->estado === 'Aprobado') {
            return redirect()->back()->with('error', 'No se puede modificar una planilla aprobada.');
        }

        $planilla->update([
            'observaciones' => $request->observaciones,
            'responsable_id' => $request->responsable_id,
        ]);

        return redirect()->route('horas-extras.show', $id)
            ->with('success', 'Información general actualizada.');
    }

    public function aprobarPlanilla($id)
    {
        $planilla = HoraExtra::findOrFail($id);

        $planilla->update([
            'estado' => 'Aprobado',
            'responsable_id' => Auth::id(),
        ]);

        return redirect()->route('horas-extras.show', $id)
            ->with('success', 'Planilla aprobada correctamente.');
    }

    public function destroy($id)
    {
        $planilla = HoraExtra::findOrFail($id);
        $planilla->delete();

        return redirect()->route('horas-extras.index')
            ->with('success', 'Planilla eliminada correctamente.');
    }

    public function generarPdf($id)
    {
        $planilla = HoraExtra::with(['user', 'responsable', 'detalles' => function($q) {
            $q->orderBy('fecha', 'asc');
        }])->findOrFail($id);

        $pdf = Pdf::loadView('administracion.horas_extras.pdf', compact('planilla'))
            ->setPaper('letter', 'landscape');

        return $pdf->stream('Planilla_Horas_Extras_'.$planilla->id.'.pdf');
    }
}
