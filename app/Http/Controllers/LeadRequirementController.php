<?php

namespace App\Http\Controllers;

use App\Models\LeadRequirement;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadRequirementController extends Controller
{
    private function esAdminOEncargado()
    {
        $u = Auth::user();
        return $u && ($u->es_admin || $u->es_encargado);
    }

    public function index(Request $request)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $query = LeadRequirement::with(['lead', 'user', 'asignado']);

        if ($request->filled('lead_id')) {
            $query->where('lead_id', $request->lead_id);
        }

        if ($request->filled('status')) {
            $query->where('estado', $request->status);
        }

        $requirements = $query->orderByDesc('created_at')->paginate(20);
        $leads = Lead::all();

        return view('lead_requirements.index', compact('requirements', 'leads'));
    }

    public function create(Request $request)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $leads = Lead::all();
        $users = User::all();
        $selected_lead = $request->lead_id;

        return view('lead_requirements.create', compact('leads', 'users', 'selected_lead'));
    }

    public function store(Request $request)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'descripcion' => 'required|string',
            'estado' => 'required|string',
            'asignado_id' => 'nullable|exists:users,id',
        ]);

        LeadRequirement::create([
            'lead_id' => $request->lead_id,
            'descripcion' => $request->descripcion,
            'estado' => $request->estado,
            'asignado_id' => $request->asignado_id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('lead-requirements.index')->with('success', 'Requerimiento comercial creado correctamente.');
    }

    public function edit($id)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $requirement = LeadRequirement::findOrFail($id);
        $leads = Lead::all();
        $users = User::all();

        return view('lead_requirements.edit', compact('requirement', 'leads', 'users'));
    }

    public function update(Request $request, $id)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $requirement = LeadRequirement::findOrFail($id);

        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'descripcion' => 'required|string',
            'estado' => 'required|string',
            'asignado_id' => 'nullable|exists:users,id',
        ]);

        $requirement->update($request->all());

        return redirect()->route('lead-requirements.index')->with('success', 'Requerimiento comercial actualizado correctamente.');
    }

    public function destroy($id)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $requirement = LeadRequirement::findOrFail($id);
        $requirement->delete();

        return redirect()->route('lead-requirements.index')->with('success', 'Requerimiento comercial eliminado correctamente.');
    }
}
