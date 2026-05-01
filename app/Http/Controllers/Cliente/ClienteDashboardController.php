<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\RequerimientoCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteDashboardController extends Controller
{
    public function index(Request $request)
    {
        $loginCliente = Auth::guard('cliente')->user();
        $cliente = $loginCliente->cliente;

        // Estadísticas de Requerimientos (Totales de siempre)
        $stats = [
            'total' => RequerimientoCliente::where('cliente_id', $cliente->id)->where('estado_id', '!=', 6)->count(),
            'pendientes' => RequerimientoCliente::where('cliente_id', $cliente->id)->where('estado_id', 1)->count(),
            'en_progreso' => RequerimientoCliente::where('cliente_id', $cliente->id)->where('estado_id', 2)->count(),
            'completados' => RequerimientoCliente::where('cliente_id', $cliente->id)->where('estado_id', 3)->count(),
        ];

        // Métricas de Iguala
        $metrics = $cliente->getMetricasIguala();

        // Requerimientos DEL MES
        $query = RequerimientoCliente::where('cliente_id', $cliente->id)
            ->where('estado_id', '!=', 6)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->with(['estadoRequerimiento', 'asignado']);

        if ($request->filled('estado')) {
            $query->where('estado_id', $request->estado);
        }

        $requirements = $query->orderBy('created_at', 'desc')->get();
        $estados = \App\Models\EstadoRequerimiento::where('id', '!=', 6)->get();

        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $nombreMes = $meses[now()->month];

        return view('cliente.dashboard', compact('cliente', 'stats', 'metrics', 'requirements', 'estados', 'nombreMes'));
    }

    public function historial(Request $request)
    {
        $loginCliente = Auth::guard('cliente')->user();
        $cliente = $loginCliente->cliente;

        $query = RequerimientoCliente::where('cliente_id', $cliente->id)
            ->where('estado_id', '!=', 6)
            ->with(['estadoRequerimiento', 'asignado']);

        if ($request->filled('estado')) {
            $query->where('estado_id', $request->estado);
        }

        if ($request->filled('mes')) {
            $query->whereMonth('created_at', $request->mes);
        }

        if ($request->filled('anio')) {
            $query->whereYear('created_at', $request->anio);
        }

        if ($request->filled('search')) {
            $query->where('texto_imagen', 'like', '%' . $request->search . '%');
        }

        $requirements = $query->orderBy('created_at', 'desc')->paginate(15);
        $estados = \App\Models\EstadoRequerimiento::where('id', '!=', 6)->get();
        
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        return view('cliente.historial', compact('requirements', 'estados', 'meses'));
    }

    public function showRequerimiento($id)
    {
        $loginCliente = Auth::guard('cliente')->user();
        $cliente = $loginCliente->cliente;

        $requerimiento = RequerimientoCliente::where('cliente_id', $cliente->id)
            ->where('id', $id)
            ->with(['novedades' => function($q) {
                $q->where('tipo', 'interno')->with('user')->orderBy('created_at', 'desc');
            }, 'estadoRequerimiento'])
            ->firstOrFail();

        return view('cliente.show_requerimiento', compact('requerimiento'));
    }

    public function novedades()
    {
        $loginCliente = Auth::guard('cliente')->user();
        $cliente = $loginCliente->cliente;

        $novedades = \App\Models\NovedadRequerimiento::whereHas('requerimiento', function($q) use ($cliente) {
                $q->where('cliente_id', $cliente->id);
            })
            ->where('tipo', 'interno') // 'interno' is shared with client according to show.blade logic
            ->with(['requerimiento', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('cliente.novedades', compact('novedades'));
    }

    public function storeNovedad(Request $request, $id)
    {
        $loginCliente = Auth::guard('cliente')->user();
        $cliente = $loginCliente->cliente;

        $requerimiento = RequerimientoCliente::where('cliente_id', $cliente->id)
            ->where('id', $id)
            ->firstOrFail();

        $request->validate([
            'novedad' => 'required|string',
            'adjunto' => 'nullable|file|max:10240', // 10MB
        ]);

        $data = [
            'requerimiento_id' => $requerimiento->id,
            'cliente_id' => $cliente->id,
            'user_id' => null, // Opcional, o podrías usar un ID de sistema
            'novedad' => $request->novedad,
            'tipo' => 'interno', // 'interno' es la categoría compartida con el cliente
        ];

        if ($request->hasFile('adjunto')) {
            $path = $request->file('adjunto')->store('novedades/' . $requerimiento->id, 'public');
            $data['adjunto'] = $path;
            $data['nombre_original'] = $request->file('adjunto')->getClientOriginalName();
        }

        \App\Models\NovedadRequerimiento::create($data);

        return back()->with('success', 'Novedad enviada correctamente.');
    }
}
