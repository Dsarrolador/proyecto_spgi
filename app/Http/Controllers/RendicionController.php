<?php

namespace App\Http\Controllers;

use App\Models\Rendicion;
use App\Models\RendicionGasto;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RendicionController extends Controller
{
    public function index()
    {
        $rendiciones = Rendicion::with(['user', 'responsable'])->orderBy('created_at', 'desc')->get();
        return view('rendiciones.index', compact('rendiciones'));
    }

    public function create()
    {
        $usuarios = \App\Models\User::orderBy('name')->get();
        return view('rendiciones.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
        ]);

        $rendicion = Rendicion::create([
            'user_id' => $request->user_id,
            'responsable_id' => null,
            'titulo' => $request->titulo,
            'estado' => 'Borrador',
        ]);

        return redirect()->route('rendiciones.show', $rendicion->id)->with('success', 'Rendición creada. Ahora puedes agregar gastos.');
    }

    public function show($id)
    {
        $rendicion = Rendicion::with(['user', 'responsable', 'gastos' => function($q) {
            $q->with('metodoPago')->orderBy('fecha', 'asc');
        }])->findOrFail($id);
        
        $metodosPago = \App\Models\MetodoPago::orderBy('nombre')->get();
        $usuarios = \App\Models\User::orderBy('name')->get();
        
        return view('rendiciones.show', compact('rendicion', 'metodosPago', 'usuarios'));
    }

    public function storeGasto(Request $request, $id)
    {
        $request->validate([
            'fecha' => 'required|date',
            'concepto' => 'required|string|max:255',
            'proveedor' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
            'tarjeta_ultimos_4' => 'nullable|string|size:4|regex:/^[0-9]+$/',
            'observaciones' => 'nullable|string'
        ]);

        // Validar si requiere tarjeta
        $metodo = \App\Models\MetodoPago::findOrFail($request->metodo_pago_id);
        if ($metodo->requiere_tarjeta && empty($request->tarjeta_ultimos_4)) {
            return redirect()->back()->withErrors(['tarjeta_ultimos_4' => 'Debe ingresar los últimos 4 dígitos de la tarjeta.'])->withInput();
        }

        RendicionGasto::create([
            'rendicion_id' => $id,
            'fecha' => $request->fecha,
            'concepto' => $request->concepto,
            'proveedor' => $request->proveedor,
            'monto' => $request->monto,
            'metodo_pago_id' => $request->metodo_pago_id,
            'tarjeta_ultimos_4' => $metodo->requiere_tarjeta ? $request->tarjeta_ultimos_4 : null,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('rendiciones.show', $id)->with('success', 'Gasto agregado correctamente.');
    }

    public function deleteGasto($id, $gasto_id)
    {
        $gasto = RendicionGasto::where('rendicion_id', $id)->findOrFail($gasto_id);
        $gasto->delete();

        return redirect()->route('rendiciones.show', $id)->with('success', 'Gasto eliminado.');
    }

    public function destroy($id)
    {
        $rendicion = Rendicion::findOrFail($id);
        $rendicion->delete();
        return redirect()->route('rendiciones.index')->with('success', 'Rendición eliminada.');
    }

    public function generarPdf($id)
    {
        $rendicion = Rendicion::with(['user', 'responsable', 'gastos' => function($q) {
            $q->with('metodoPago')->orderBy('fecha', 'asc');
        }])->findOrFail($id);

        $pdf = Pdf::loadView('rendiciones.pdf', compact('rendicion'))->setPaper('letter', 'landscape');
        return $pdf->stream('Rendicion_Gastos_'.$rendicion->id.'.pdf');
    }

    public function updateGeneralInfo(Request $request, $id)
    {
        $request->validate([
            'fecha_aprobacion' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        $rendicion = Rendicion::findOrFail($id);
        $rendicion->update([
            'fecha_aprobacion' => $request->fecha_aprobacion,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('rendiciones.show', $id)->with('success', 'Información general actualizada.');
    }

    public function storeMetodoPago(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:metodos_pago,nombre|max:255',
            'requiere_tarjeta' => 'required|boolean',
        ]);

        $metodo = \App\Models\MetodoPago::create([
            'nombre' => $request->nombre,
            'requiere_tarjeta' => $request->requiere_tarjeta,
        ]);

        return response()->json([
            'success' => true,
            'metodo' => $metodo
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|string|in:Borrador,Enviado,Aprobado,Rechazado',
        ]);

        $rendicion = Rendicion::findOrFail($id);
        $rendicion->update([
            'estado' => $request->estado,
        ]);

        return response()->json(['success' => true]);
    }
}
