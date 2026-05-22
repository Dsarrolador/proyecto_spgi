<?php

namespace App\Http\Controllers;

use App\Models\TipoSoporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoSoporteController extends Controller
{
    public function index()
    {
        $tipos = TipoSoporte::orderBy('nombre')->get();
        return view('mantenimiento.tipo_soporte.index', compact('tipos'));
    }

    public function create()
    {
        return view('mantenimiento.tipo_soporte.create');
    }

    /**
     * GUARDAR NUEVO TIPO DE SOPORTE
     */
    

public function store(Request $request)
{
    // ❌ ELIMINAR CUALQUIER CAMPO CON TILDE (FANTASMA)
    $request->request->remove('descripción');

    $request->validate([
        'nombre' => 'required|string|max:150|unique:tipo_soporte,nombre',
        'descripcion' => 'nullable|string',
        'activo' => 'nullable',
    ]);

    // ✅ INSERT DIRECTO (CONTROL TOTAL)
    DB::table('tipo_soporte')->insert([
        'nombre'       => $request->input('nombre'),
        'descripcion'  => $request->input('descripcion'),
        'activo'       => $request->has('activo'),
        'created_at'   => now(),
        'updated_at'   => now(),
    ]);

    return redirect()
        ->route('mantenimiento.tipo-soporte.index')
        ->with('success', 'Tipo de soporte creado correctamente.');
}

    public function storeAjax(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150|unique:tipo_soporte,nombre',
        ]);

        $id = DB::table('tipo_soporte')->insertGetId([
            'nombre'       => $request->input('nombre'),
            'descripcion'  => null,
            'activo'       => true,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $id,
                'nombre' => $request->input('nombre')
            ]
        ]);
    }


    public function edit($id)
    {
        $tipo = TipoSoporte::findOrFail($id);
        return view('mantenimiento.tipo_soporte.edit', compact('tipo'));
    }

    /**
     * ACTUALIZAR TIPO DE SOPORTE
     */
    public function update(Request $request, $id)
    {
        $tipo = TipoSoporte::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:150|unique:tipo_soporte,nombre,' . $tipo->id,
            'descripcion' => 'nullable|string',
            'activo' => 'nullable',
        ]);

        $tipo->update([
            'nombre'      => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'), // 🔒 SIN TILDE
            'activo'      => $request->has('activo'),
        ]);

        return redirect()
            ->route('mantenimiento.tipo-soporte.index')
            ->with('success', 'Tipo de soporte actualizado correctamente.');
    }

    public function destroy($id)
    {
        $tipo = TipoSoporte::findOrFail($id);
        $tipo->delete();

        return redirect()
            ->route('mantenimiento.tipo-soporte.index')
            ->with('success', 'Tipo de soporte eliminado correctamente.');
    }
}
