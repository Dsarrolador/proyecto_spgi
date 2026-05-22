<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\NotificacionSistema;
use App\Models\LeadCalculation;
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
            'persona_contacto' => 'nullable|string|max:255',
            'direccion' => 'nullable|string',
            'contacto' => 'nullable|string|max:255',
            'correo' => 'nullable|email|max:255',
            'cotizacion_pdf' => 'nullable|mimes:pdf,xlsx,xls|max:10240',
            'total_estimado' => 'nullable|numeric',
            'observaciones' => 'nullable|string',
            'calculo_data' => 'nullable|json',
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

        $lead = Lead::with('calculations')->findOrFail($id);
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
            'persona_contacto' => 'nullable|string|max:255',
            'direccion' => 'nullable|string',
            'contacto' => 'nullable|string|max:255',
            'correo' => 'nullable|email|max:255',
            'cotizacion_pdf' => 'nullable|mimes:pdf,xlsx,xls|max:10240',
            'total_estimado' => 'nullable|numeric',
            'observaciones' => 'nullable|string',
            'status' => 'required|string',
            'calculo_data' => 'nullable|json',
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

    public function calculadora($id, Request $request)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $lead = Lead::findOrFail($id);
        $calculation = null;
        if ($request->filled('calculation_id')) {
            $calculation = \App\Models\LeadCalculation::where('lead_id', $id)->findOrFail($request->calculation_id);
        }
        
        return view('leads.calculadora', compact('lead', 'calculation'));
    }

    public function saveCalculo(Request $request, $id)
    {
        if (!$this->esAdminOEncargado()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $lead = Lead::findOrFail($id);

        $request->validate([
            'total_estimado' => 'required|numeric',
            'calculo_data' => 'required',
            'nombre_calculo' => 'required|string|max:255',
        ]);

        $calculo_data = $request->calculo_data;
        if (is_string($calculo_data)) {
            $calculo_data = json_decode($calculo_data, true);
        }

        $calculation = null;
        if ($request->filled('calculation_id')) {
            $calculation = \App\Models\LeadCalculation::where('lead_id', $lead->id)->find($request->calculation_id);
        }

        if ($calculation) {
            $calculation->update([
                'nombre' => $request->nombre_calculo,
                'total_estimado' => $request->total_estimado,
                'calculo_data' => $calculo_data,
            ]);
        } else {
            $calculation = \App\Models\LeadCalculation::create([
                'lead_id' => $lead->id,
                'nombre' => $request->nombre_calculo,
                'total_estimado' => $request->total_estimado,
                'calculo_data' => $calculo_data,
            ]);
        }

        // Sincronizar el total_estimado del lead con la SUMA de todos sus cálculos realizados
        $nuevo_total = \App\Models\LeadCalculation::where('lead_id', $lead->id)->sum('total_estimado');
        
        $lead->update([
            'total_estimado' => $nuevo_total,
            'calculo_data' => $calculo_data, // Mantenemos el último para compatibilidad visual
        ]);

        return response()->json(['success' => true, 'total_estimado' => $nuevo_total, 'calculation_id' => $calculation->id]);
    }

    public function indexCalculos(Request $request)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $query = LeadCalculation::with('lead');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $calculations = $query->latest()->paginate(15);
        return view('leads.index_calculos', compact('calculations'));
    }

    public function uploadFiles(Request $request, $id)
    {
        $request->validate([
            'cotizacion_files.*' => 'required|file|mimes:pdf,xlsx,xls,doc,docx|max:10240',
        ]);

        $lead = Lead::findOrFail($id);
        $calculation_id = $request->input('calculation_id');
        
        $wasRealizado = ($lead->status === 'Realizado');

        if ($request->hasFile('cotizacion_files')) {
            foreach ($request->file('cotizacion_files') as $file) {
                $path = $file->store('cotizaciones', 'public');
                \App\Models\LeadFile::create([
                    'lead_id' => $lead->id,
                    'calculation_id' => $calculation_id,
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => $file->getClientOriginalExtension(),
                ]);
            }
        }
        
        // Si hay un cálculo específico, actualizamos su estado individual
        if ($calculation_id) {
            $calc = LeadCalculation::find($calculation_id);
            if ($calc && $calc->status !== 'Realizado') {
                $calc->status = 'En proceso';
                $calc->save();
            }
        } elseif (!$wasRealizado) {
            $lead->status = 'En proceso';
            $lead->save();
        }

        // Notificar a Admin y Encargados sobre la actualización del archivo
        $admins = User::whereIn('cod_roleUser', [1, 2])
            ->orWhereHas('role', function($q) {
                $q->where('nombre', 'like', '%admin%')
                  ->orWhere('nombre', 'like', '%encargado%');
            })->get();
        
        $sender = Auth::user();

        foreach ($admins as $admin) {
            NotificacionSistema::create([
                'user_id' => $admin->id,
                'sender_id' => $sender->id ?? null,
                'titulo' => 'Cotización Actualizada',
                'mensaje' => "El comercial " . ($sender->name ?? 'Sistema') . " ha actualizado el archivo de cotización del lead: {$lead->nombre}" . ($wasRealizado ? " (Estado regresó a En proceso)." : "."),
                'url' => route('leads.indexCalculos'),
            ]);
        }
        return back()->with('success', 'Documentos adjuntados correctamente.');
    }
    
    public function downloadFile($id)
    {
        $file = \App\Models\LeadFile::findOrFail($id);
        
        if (!Storage::disk('public')->exists($file->path)) {
            return back()->with('error', 'El archivo no existe en el servidor.');
        }

        return Storage::disk('public')->response($file->path);
    }

    public function serveFile(Request $request)
    {
        $path = $request->query('path');
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404);
        }
        return Storage::disk('public')->response($path);
    }

    public function deleteFile($file_id)
    {
        $file = \App\Models\LeadFile::findOrFail($file_id);
        
        if (!$this->esAdminOEncargado()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        \Illuminate\Support\Facades\Storage::disk('public')->delete($file->path);
        $file->delete();

        return back()->with('success', 'Archivo eliminado correctamente.');
    }

    public function validar($id)
    {
        try {
            return DB::transaction(function() use ($id) {
                $lead = Lead::findOrFail($id);
                $lead->status = 'Realizado';
                $lead->save();

                // Notificar a Admin y Encargados
                $admins = User::whereIn('cod_roleUser', [1, 2])
                    ->orWhereHas('role', function($q) {
                        $q->where('nombre', 'like', '%admin%')
                          ->orWhere('nombre', 'like', '%encargado%');
                    })->get();
                
                $sender = Auth::user();

                foreach ($admins as $admin) {
                    NotificacionSistema::create([
                        'user_id' => $admin->id,
                        'sender_id' => $sender->id ?? null,
                        'titulo' => 'Cotización Validada',
                        'mensaje' => "El comercial " . ($sender->name ?? 'Sistema') . " ha validado la cotización del lead: {$lead->nombre}.",
                        'url' => route('leads.indexCalculos'),
                    ]);
                }

                return response()->json(['success' => true]);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function aprobar($id)
    {
        if (!$this->esAdminOEncargado()) return response()->json(['error' => 'No autorizado'], 403);

        $lead = Lead::findOrFail($id);
        $lead->status = 'Ganado';
        $lead->save();

        // Notificar al Comercial (creador del lead)
        if ($lead->user_id) {
            NotificacionSistema::create([
                'user_id' => $lead->user_id,
                'sender_id' => Auth::id(),
                'titulo' => 'Cotización APROBADA',
                'mensaje' => "Tu cotización para el lead {$lead->nombre} ha sido aprobada por la administración.",
                'url' => route('leads.show', $lead->id),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function rechazar(Request $request, $id)
    {
        if (!$this->esAdminOEncargado()) return response()->json(['error' => 'No autorizado'], 403);

        $lead = Lead::findOrFail($id);
        $lead->status = 'Pendiente'; // Vuelve a pendiente para corrección
        $lead->save();

        if ($lead->user_id) {
            NotificacionSistema::create([
                'user_id' => $lead->user_id,
                'sender_id' => Auth::id(),
                'titulo' => 'Cotización RECHAZADA',
                'mensaje' => "Tu cotización para {$lead->nombre} ha sido rechazada. Motivo: " . ($request->motivo ?? 'No especificado'),
                'url' => route('leads.calculadora', $lead->id),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function validarCalculation($id)
    {
        if (!$this->esAdminOEncargado()) return response()->json(['error' => 'No autorizado'], 403);

        $calculation = LeadCalculation::findOrFail($id);
        $calculation->status = 'Realizado';
        $calculation->save();

        return response()->json(['success' => true]);
    }

    public function getCalculationDetails($id, $calc_id)
    {
        if (!$this->esAdminOEncargado()) return response()->json(['error' => 'No autorizado'], 403);

        $calculation = LeadCalculation::where('lead_id', $id)->findOrFail($calc_id);
        return response()->json($calculation);
    }

    public function deleteCalculation($id)
    {
        if (!$this->esAdminOEncargado()) return response()->json(['error' => 'No autorizado'], 403);

        $calculation = \App\Models\LeadCalculation::findOrFail($id);
        $lead_id = $calculation->lead_id;
        $calculation->delete();

        // Recalcular total del lead tras eliminar
        $nuevo_total = \App\Models\LeadCalculation::where('lead_id', $lead_id)->sum('total_estimado');
        Lead::where('id', $lead_id)->update(['total_estimado' => $nuevo_total]);

        return response()->json(['success' => true]);
    }
}
