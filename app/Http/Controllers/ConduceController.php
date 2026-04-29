<?php

namespace App\Http\Controllers;

use App\Models\Conduce;
use App\Models\ConduceItem;
use App\Models\RequerimientoCliente;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ConduceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'nullable|string',
            'requerimiento_id' => 'required|exists:requerimiento_cliente,id',
            'fecha' => 'required|date',
            'trabajo_realizar' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'contacto_id' => 'nullable',
            'contacto_manual' => 'nullable|string',
            'hora_entrada' => 'nullable|string',
            'hora_salida' => 'nullable|string',
            'cantidad_horas' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.cantidad' => 'required|numeric',
            'items.*.descripcion' => 'required|string',
            'items.*.num_cotizacion' => 'nullable|string',
            'items.*.facturar' => 'nullable|string',
        ]);

        $requerimiento = RequerimientoCliente::findOrFail($request->requerimiento_id);

        $contacto_id = null;
        $contacto_nombre = $request->contacto_manual;

        if ($request->contacto_id && is_numeric($request->contacto_id)) {
            $contacto = \App\Models\LibretaContacto::find($request->contacto_id);
            if ($contacto) {
                $contacto_id = $contacto->id;
                $contacto_nombre = $contacto->nombre;
            }
        }

        $conduce = Conduce::create([
            'tipo' => $request->tipo ?? 'trabajo',
            'requerimiento_id' => $requerimiento->id,
            'cliente_id' => $requerimiento->cliente_id,
            'contacto_id' => $contacto_id,
            'contacto_nombre' => $contacto_nombre,
            'fecha' => $request->fecha,
            'trabajo_realizar' => $request->trabajo_realizar,
            'observaciones' => $request->observaciones,
            'hora_entrada' => $request->hora_entrada,
            'hora_salida' => $request->hora_salida,
            'cantidad_horas' => $request->cantidad_horas,
        ]);

        foreach ($request->items as $item) {
            ConduceItem::create([
                'conduce_id' => $conduce->id,
                'cantidad' => $item['cantidad'],
                'descripcion' => $item['descripcion'],
                'num_cotizacion' => $item['num_cotizacion'] ?? null,
                'facturar' => isset($item['facturar']) && ($item['facturar'] === 'on' || $item['facturar'] === '1' || $item['facturar'] === true),
            ]);
        }

        return redirect()->route('conduces.pdf', $conduce->id);
    }

    public function generatePdf($id)
    {
        try {
            $conduce = Conduce::with(['requirement', 'cliente', 'contacto', 'items'])->findOrFail($id);
            
            $data = [
                'conduce' => $conduce,
                'fecha' => Carbon::parse($conduce->fecha)->format('d/m/Y'),
            ];

            $pdf = Pdf::loadView('conduces.pdf', $data);
            return $pdf->stream('Conduce_' . $conduce->id . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar PDF',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
