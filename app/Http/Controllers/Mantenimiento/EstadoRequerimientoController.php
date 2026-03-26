<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EstadoRequerimientoController extends Controller
{
    public function index()
    {
        $estados = \App\Models\EstadoRequerimiento::orderBy('id')->get();
        return view('mantenimiento.estados-requerimiento.index', compact('estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'color'  => 'nullable|string|max:50'
        ]);

        \App\Models\EstadoRequerimiento::create($request->all());

        return redirect()->route('mantenimiento.estados-requerimiento.index')->with('success', 'Estado creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'color'  => 'nullable|string|max:50'
        ]);

        $estado = \App\Models\EstadoRequerimiento::findOrFail($id);
        $estado->update($request->all());

        return redirect()->route('mantenimiento.estados-requerimiento.index')->with('success', 'Estado actualizado correctamente.');
    }

    public function destroy($id)
    {
        $estado = \App\Models\EstadoRequerimiento::findOrFail($id);
        
        // Prevent deleting states if they are in use? We used foreign keys to restrict? Wait, I didn't set restrict.
        // Actually, let's just delete it, or check if it's used. Since we added a foreign key without cascade or set null specifically, it will throw SQL error if used. Let's catch it.
        try {
            $estado->delete();
            return redirect()->route('mantenimiento.estados-requerimiento.index')->with('success', 'Estado eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('mantenimiento.estados-requerimiento.index')->with('error', 'No se puede eliminar porque este estado está siendo utilizado en algunos requerimientos.');
        }
    }
}
