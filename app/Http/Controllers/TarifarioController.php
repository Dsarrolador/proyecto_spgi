<?php

namespace App\Http\Controllers;

use App\Models\Tarifario;
use Illuminate\Http\Request;

class TarifarioController extends Controller
{
    public function index()
    {
        $tarifarios = Tarifario::all();
        return view('tarifarios.index', compact('tarifarios'));
    }

    public function create()
    {
        $tipoSoportes = \App\Models\TipoTarifario::orderBy('nombre')->get();
        return view('tarifarios.create', compact('tipoSoportes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion' => 'required|string|max:255',
            'basico_int' => 'nullable|string|max:255',
            'avanzado_int' => 'nullable|string|max:255',
            'basico_ext' => 'nullable|string|max:255',
            'avanzado_ext' => 'nullable|string|max:255',
            'valor' => 'nullable|string|max:255',
            'tipo_tarifario_id' => 'nullable|exists:tipo_tarifarios,id',
        ]);

        Tarifario::create($validated);

        return redirect()->route('tarifarios.index')->with('success', 'Tarifa registrada exitosamente.');
    }

    public function edit(Tarifario $tarifario)
    {
        $tipoSoportes = \App\Models\TipoTarifario::orderBy('nombre')->get();
        return view('tarifarios.edit', compact('tarifario', 'tipoSoportes'));
    }

    public function update(Request $request, Tarifario $tarifario)
    {
        $validated = $request->validate([
            'descripcion' => 'required|string|max:255',
            'basico_int' => 'nullable|string|max:255',
            'avanzado_int' => 'nullable|string|max:255',
            'basico_ext' => 'nullable|string|max:255',
            'avanzado_ext' => 'nullable|string|max:255',
            'valor' => 'nullable|string|max:255',
            'tipo_tarifario_id' => 'nullable|exists:tipo_tarifarios,id',
        ]);

        $tarifario->update($validated);

        return redirect()->route('tarifarios.index')->with('success', 'Tarifa actualizada exitosamente.');
    }

    public function destroy(Tarifario $tarifario)
    {
        $tarifario->delete();
        return redirect()->route('tarifarios.index')->with('success', 'Tarifa eliminada exitosamente.');
    }

    public function storeTipoAjax(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150|unique:tipo_tarifarios,nombre',
        ]);

        $tipo = \App\Models\TipoTarifario::create([
            'nombre' => $request->input('nombre')
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $tipo->id,
                'nombre' => $tipo->nombre
            ]
        ]);
    }
}
