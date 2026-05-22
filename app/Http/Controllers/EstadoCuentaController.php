<?php

namespace App\Http\Controllers;

use App\Models\EstadoCuenta;
use App\Models\ClienteMaestro;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class EstadoCuentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EstadoCuenta::query();

        // Apply filters
        if ($request->filled('cliente_nombre')) {
            $query->where('cliente_nombre', 'like', '%' . $request->cliente_nombre . '%');
        }

        if ($request->filled('moneda')) {
            $query->where('moneda', $request->moneda);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        // Retrieve and sort
        // Order by cliente_nombre (alphabetically) then by fecha (ascending)
        $records = $query->orderBy('cliente_nombre', 'asc')
                         ->orderBy('fecha', 'asc')
                         ->get();

        // If filtering by calculated status (PAGO, VENCIDO, PENDIENTE)
        // Since getEstadoCalculadoAttribute is a dynamic/appended attribute, we filter in memory
        if ($request->filled('estado')) {
            $estado = $request->estado;
            $records = $records->filter(function ($item) use ($estado) {
                return $item->estado_calculado === $estado;
            });
        }

        // Group by cliente_nombre
        $groupedRecords = $records->groupBy('cliente_nombre');

        // Fetch all client names for quick filter autocomplete
        $clientesFiltro = EstadoCuenta::select('cliente_nombre')
            ->distinct()
            ->orderBy('cliente_nombre', 'asc')
            ->pluck('cliente_nombre');

        return view('administracion.estado_cuentas.index', compact('groupedRecords', 'clientesFiltro'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientesMaestros = ClienteMaestro::orderBy('nombre', 'asc')->get();
        return view('administracion.estado_cuentas.create', compact('clientesMaestros'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'cliente_nombre' => 'required|string|max:255',
            'cliente_maestro_id' => 'nullable|exists:cliente_maestro,id',
            'factura_no' => 'required|string|max:100',
            'nfc' => 'nullable|string|max:100',
            'fecha' => 'required|date',
            'fecha_vencimiento' => 'required|date',
            'producto' => 'required|string|max:255',
            'balance' => 'required|numeric|min:0',
            'moneda' => 'required|in:DOP,USD',
            'tasa_cambio' => 'nullable|required_if:moneda,USD|numeric|min:0',
        ];

        $request->validate($rules);

        EstadoCuenta::create($request->all());

        return redirect()->route('estado-cuentas.index')
            ->with('success', 'Factura registrada exitosamente en el Estado de Cuenta.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $record = EstadoCuenta::findOrFail($id);
        $clientesMaestros = ClienteMaestro::orderBy('nombre', 'asc')->get();
        return view('administracion.estado_cuentas.edit', compact('record', 'clientesMaestros'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $record = EstadoCuenta::findOrFail($id);

        $rules = [
            'cliente_nombre' => 'required|string|max:255',
            'cliente_maestro_id' => 'nullable|exists:cliente_maestro,id',
            'factura_no' => 'required|string|max:100',
            'nfc' => 'nullable|string|max:100',
            'fecha' => 'required|date',
            'fecha_vencimiento' => 'required|date',
            'producto' => 'required|string|max:255',
            'balance' => 'required|numeric|min:0',
            'moneda' => 'required|in:DOP,USD',
            'tasa_cambio' => 'nullable|required_if:moneda,USD|numeric|min:0',
            'fecha_pago' => 'nullable|date',
            'fecha_aplicado' => 'nullable|date',
            'recibo_no' => 'nullable|string|max:100',
            'total_pagado' => 'nullable|numeric|min:0',
        ];

        $request->validate($rules);

        $data = $request->all();

        // If fecha_pago is provided but total_pagado is empty, default total_pagado to balance
        if ($request->filled('fecha_pago') && !$request->filled('total_pagado')) {
            $data['total_pagado'] = $request->balance;
        }

        // If fecha_pago is cleared, clear payment fields
        if (!$request->filled('fecha_pago')) {
            $data['fecha_pago'] = null;
            $data['fecha_aplicado'] = null;
            $data['recibo_no'] = null;
            $data['total_pagado'] = null;
        }

        $record->update($data);

        return redirect()->route('estado-cuentas.index')
            ->with('success', 'Registro de Estado de Cuenta actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $record = EstadoCuenta::findOrFail($id);
        $record->delete();

        return redirect()->route('estado-cuentas.index')
            ->with('success', 'Registro eliminado del Estado de Cuenta.');
    }

    /**
     * Export Statement of Accounts to landscape PDF
     */
    public function generarPdf(Request $request)
    {
        $query = EstadoCuenta::query();

        // Apply same filters
        if ($request->filled('cliente_nombre')) {
            $query->where('cliente_nombre', 'like', '%' . $request->cliente_nombre . '%');
        }

        if ($request->filled('moneda')) {
            $query->where('moneda', $request->moneda);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $records = $query->orderBy('cliente_nombre', 'asc')
                         ->orderBy('fecha', 'asc')
                         ->get();

        if ($request->filled('estado')) {
            $estado = $request->estado;
            $records = $records->filter(function ($item) use ($estado) {
                return $item->estado_calculado === $estado;
            });
        }

        $groupedRecords = $records->groupBy('cliente_nombre');
        $fechaReporte = Carbon::today()->format('d/m/Y');

        $pdf = Pdf::loadView('administracion.estado_cuentas.pdf', compact('groupedRecords', 'fechaReporte'))
                  ->setPaper('letter', 'landscape');

        return $pdf->stream('Estado_de_Cuenta_' . Carbon::today()->format('d_m_Y') . '.pdf');
    }
}
