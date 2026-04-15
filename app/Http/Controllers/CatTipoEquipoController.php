<?php

namespace App\Http\Controllers;

use App\Models\CatTipoEquipo;
use Illuminate\Http\Request;

class CatTipoEquipoController extends Controller
{
    public function index()
    {
        $tipos = CatTipoEquipo::orderBy('nombre')->get();
        return view('mantenimiento.equipos.tipos_index', compact('tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:cat_tipos_equipo,nombre',
            'descripcion' => 'nullable|string|max:255',
        ]);

        CatTipoEquipo::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => $request->has('activo') || $request->activo == 1,
        ]);

        return redirect()->back()->with('success', 'Tipo de equipo creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $tipo = CatTipoEquipo::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100|unique:cat_tipos_equipo,nombre,' . $id,
            'descripcion' => 'nullable|string|max:255',
        ]);

        $tipo->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => $request->has('activo') || $request->activo == 1,
        ]);

        return redirect()->back()->with('success', 'Tipo de equipo actualizado correctamente.');
    }

    public function destroy($id)
    {
        $tipo = CatTipoEquipo::findOrFail($id);
        
        // Se podría agregar validación si existen equipos de este tipo
        
        $tipo->delete();

        return redirect()->back()->with('success', 'Tipo de equipo eliminado.');
    }
}
