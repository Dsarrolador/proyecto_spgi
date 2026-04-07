<?php

namespace App\Http\Controllers;

use App\Models\CategoriaIguala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaIgualaController extends Controller
{
    public function index()
    {
        $igualas = CategoriaIguala::orderBy('nombre')->get();
        return view('mantenimiento.iguala.index', compact('igualas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150|unique:categorias_iguala,nombre',
            'descripcion' => 'nullable|string',
            'activo' => 'nullable',
        ]);

        DB::table('categorias_iguala')->insert([
            'nombre'       => $request->input('nombre'),
            'descripcion'  => $request->input('descripcion'),
            'activo'       => $request->has('activo'),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return redirect()
            ->route('mantenimiento.iguala.index')
            ->with('success', 'Iguala creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $iguala = CategoriaIguala::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:150|unique:categorias_iguala,nombre,' . $iguala->id,
            'descripcion' => 'nullable|string',
            'activo' => 'nullable',
        ]);

        $iguala->update([
            'nombre'      => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
            'activo'      => $request->has('activo'),
        ]);

        return redirect()
            ->route('mantenimiento.iguala.index')
            ->with('success', 'Iguala actualizada correctamente.');
    }

    public function destroy($id)
    {
        $iguala = CategoriaIguala::findOrFail($id);
        $iguala->delete();

        return redirect()
            ->route('mantenimiento.iguala.index')
            ->with('success', 'Iguala eliminada correctamente.');
    }
}
