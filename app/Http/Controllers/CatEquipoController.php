<?php

namespace App\Http\Controllers;

use App\Models\CatEquipo;
use App\Models\CatTipoEquipo;
use Illuminate\Http\Request;

class CatEquipoController extends Controller
{
    public function index()
    {
        $equipos = CatEquipo::with('tipoEquipo')->orderBy('nombre')->get();
        $tipos = CatTipoEquipo::where('activo', true)->orderBy('nombre')->get();
        return view('mantenimiento.equipos.index', compact('equipos', 'tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'tipo_equipo_id' => 'required|exists:cat_tipos_equipo,id',
        ]);

        $data = $request->all();
        $tipo = CatTipoEquipo::find($request->tipo_equipo_id);
        $data['tipo'] = $tipo->nombre;

        CatEquipo::create($data);

        return back()->with('success', 'Equipo creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required',
            'tipo_equipo_id' => 'required|exists:cat_tipos_equipo,id',
        ]);

        $equipo = CatEquipo::findOrFail($id);
        $data = $request->all();
        $tipo = CatTipoEquipo::find($request->tipo_equipo_id);
        $data['tipo'] = $tipo->nombre;
        
        $equipo->update($data);

        return back()->with('success', 'Equipo actualizado.');
    }

    public function destroy($id)
    {
        $equipo = CatEquipo::findOrFail($id);
        $equipo->delete();

        return back()->with('success', 'Equipo eliminado.');
    }
}
