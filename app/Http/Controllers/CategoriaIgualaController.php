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
            'cantidad_soporte_remoto' => 'nullable|integer|min:0',
            'cantidad_visitas'        => 'nullable|integer|min:0',
            'mantenimiento_sw_hw'     => 'nullable',
            'equipo_prestamo'         => 'nullable',
            'asistencia_vip'          => 'nullable',
        ]);

        CategoriaIguala::create([
            'nombre'                  => $request->input('nombre'),
            'descripcion'             => $request->input('descripcion'),
            'activo'                  => $request->has('activo'),
            'cantidad_soporte_remoto' => $request->input('cantidad_soporte_remoto', 0),
            'cantidad_visitas'        => $request->input('cantidad_visitas', 0),
            'mantenimiento_sw_hw'     => $request->has('mantenimiento_sw_hw'),
            'equipo_prestamo'         => $request->has('equipo_prestamo'),
            'asistencia_vip'          => $request->has('asistencia_vip'),
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
            'cantidad_soporte_remoto' => 'nullable|integer|min:0',
            'cantidad_visitas'        => 'nullable|integer|min:0',
            'mantenimiento_sw_hw'     => 'nullable',
            'equipo_prestamo'         => 'nullable',
            'asistencia_vip'          => 'nullable',
        ]);

        $iguala->update([
            'nombre'                  => $request->input('nombre'),
            'descripcion'             => $request->input('descripcion'),
            'activo'                  => $request->has('activo'),
            'cantidad_soporte_remoto' => $request->input('cantidad_soporte_remoto', 0),
            'cantidad_visitas'        => $request->input('cantidad_visitas', 0),
            'mantenimiento_sw_hw'     => $request->has('mantenimiento_sw_hw'),
            'equipo_prestamo'         => $request->has('equipo_prestamo'),
            'asistencia_vip'          => $request->has('asistencia_vip'),
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
