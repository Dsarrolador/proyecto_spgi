<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LeadController extends Controller
{
    private function esAdminOEncargado()
    {
        $u = Auth::user();
        return $u && ($u->es_admin || $u->es_encargado);
    }

    public function bienvenido()
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        return view('leads.bienvenido');
    }

    public function reportes(Request $request)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $query = Lead::query();

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        // 1. Distribución por Status
        $statusDistribution = (clone $query)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // 2. Valor Estimado por Status
        $valuePerStatus = (clone $query)
            ->select('status', DB::raw('sum(total_estimado) as total_valor'))
            ->groupBy('status')
            ->get();

        // 3. Histórico Mensual (Últimos 12 meses)
        $historical = (clone $query)
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes"), DB::raw('count(*) as total'))
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->take(12)
            ->get();

        // 4. Totales Generales
        $totals = [
            'count' => (clone $query)->count(),
            'value' => (clone $query)->sum('total_estimado'),
            'won' => (clone $query)->where('status', 'Ganado')->count(),
        ];

        $leads = $query->orderByDesc('created_at')->get();

        return view('leads.reportes', compact('statusDistribution', 'valuePerStatus', 'historical', 'totals', 'leads'));
    }

    public function index(Request $request)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $query = Lead::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        $leads = $query->orderByDesc('created_at')->paginate(15);

        return view('leads.index', compact('leads'));
    }

    public function create()
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        return view('leads.create');
    }

    public function store(Request $request)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string',
            'contacto' => 'nullable|string|max:255',
            'correo' => 'nullable|email|max:255',
            'cotizacion_pdf' => 'nullable|mimes:pdf|max:10240',
            'total_estimado' => 'nullable|numeric',
            'observaciones' => 'nullable|string',
        ]);

        $data = $request->except('cotizacion_pdf');
        $data['user_id'] = Auth::id();

        if ($request->hasFile('cotizacion_pdf')) {
            $data['cotizacion_pdf'] = $request->file('cotizacion_pdf')->store('leads/cotizaciones', 'public');
        }

        Lead::create($data);

        return redirect()->route('leads.index')->with('success', 'Lead creado correctamente.');
    }

    public function show($id)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $lead = Lead::findOrFail($id);
        return view('leads.show', compact('lead'));
    }

    public function edit($id)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $lead = Lead::findOrFail($id);
        return view('leads.edit', compact('lead'));
    }

    public function update(Request $request, $id)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $lead = Lead::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string',
            'contacto' => 'nullable|string|max:255',
            'correo' => 'nullable|email|max:255',
            'cotizacion_pdf' => 'nullable|mimes:pdf|max:10240',
            'total_estimado' => 'nullable|numeric',
            'observaciones' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $data = $request->except('cotizacion_pdf');

        if ($request->hasFile('cotizacion_pdf')) {
            // Delete old file if exists
            if ($lead->cotizacion_pdf) {
                Storage::disk('public')->delete($lead->cotizacion_pdf);
            }
            $data['cotizacion_pdf'] = $request->file('cotizacion_pdf')->store('leads/cotizaciones', 'public');
        }

        $lead->update($data);

        return redirect()->route('leads.index')->with('success', 'Lead actualizado correctamente.');
    }

    public function destroy($id)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $lead = Lead::findOrFail($id);
        
        if ($lead->cotizacion_pdf) {
            Storage::disk('public')->delete($lead->cotizacion_pdf);
        }

        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead eliminado correctamente.');
    }
}
