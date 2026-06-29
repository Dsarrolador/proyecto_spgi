<?php

namespace App\Http\Controllers;

use App\Models\RequerimientoAdministrativo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequerimientoAdministrativoController extends Controller
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

        $query = RequerimientoAdministrativo::with(['user', 'asignado']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('titulo', 'like', '%' . $request->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->search . '%');
            });
        }

        $requerimientos = $query->orderByDesc('created_at')->paginate(15);
        $usuarios = User::orderBy('name')->get();

        return view('administracion.requerimientos_admin.index', compact('requerimientos', 'usuarios'));
    }

    public function create()
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $usuarios = User::orderBy('name')->get();
        return view('administracion.requerimientos_admin.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'prioridad' => 'required|string|in:Baja,Media,Alta',
            'estado' => 'required|string|in:Pendiente,En Proceso,Completado,Cancelado',
            'asignado_user_id' => 'nullable|integer|exists:users,id',
            'fecha_limite' => 'nullable|date',
            'es_recurrente' => 'nullable|boolean',
            'frecuencia' => 'nullable|string',
            'fecha_inicio_recurrencia' => 'nullable|date',
        ]);

        $data['user_id'] = Auth::id();
        $data['es_recurrente'] = $request->has('es_recurrente');

        $requerimiento = RequerimientoAdministrativo::create($data);

        if ($requerimiento->es_recurrente && $requerimiento->frecuencia) {
            $requerimiento->update([
                'proxima_fecha_ejecucion' => $requerimiento->calcularProximaFecha()
            ]);
        }

        return redirect()->route('requerimientos-administrativos.index')
            ->with('success', 'Requerimiento administrativo creado correctamente.');
    }

    public function edit($id)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $requerimiento = RequerimientoAdministrativo::findOrFail($id);
        $usuarios = User::orderBy('name')->get();
        return view('administracion.requerimientos_admin.edit', compact('requerimiento', 'usuarios'));
    }

    public function update(Request $request, $id)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $requerimiento = RequerimientoAdministrativo::findOrFail($id);

        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'prioridad' => 'required|string|in:Baja,Media,Alta',
            'estado' => 'required|string|in:Pendiente,En Proceso,Completado,Cancelado',
            'asignado_user_id' => 'nullable|integer|exists:users,id',
            'fecha_limite' => 'nullable|date',
            'es_recurrente' => 'nullable|boolean',
            'frecuencia' => 'nullable|string',
            'fecha_inicio_recurrencia' => 'nullable|date',
        ]);

        $data['es_recurrente'] = $request->has('es_recurrente');

        $requerimiento->update($data);

        if ($requerimiento->es_recurrente) {
            if (!$requerimiento->proxima_fecha_ejecucion) {
                $requerimiento->update([
                    'proxima_fecha_ejecucion' => $requerimiento->calcularProximaFecha()
                ]);
            }
        } else {
            $requerimiento->update([
                'proxima_fecha_ejecucion' => null,
                'frecuencia' => null,
                'fecha_inicio_recurrencia' => null,
            ]);
        }

        return redirect()->route('requerimientos-administrativos.index')
            ->with('success', 'Requerimiento administrativo actualizado correctamente.');
    }

    public function destroy($id)
    {
        if (!$this->esAdminOEncargado()) {
            return redirect()->route('bienvenido')->with('error', 'Acceso denegado.');
        }

        $requerimiento = RequerimientoAdministrativo::findOrFail($id);
        $requerimiento->delete();

        return redirect()->route('requerimientos-administrativos.index')
            ->with('success', 'Requerimiento administrativo eliminado correctamente.');
    }
}
