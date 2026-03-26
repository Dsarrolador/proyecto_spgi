<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class RolesController extends Controller
{
    public function index()
    {
        $roles = Roles::orderBy('nombre')->get();
        return view('mantenimiento.roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:roles,nombre',
            'descripcion' => 'nullable|string|max:255',
            'activo' => 'required|boolean',
        ]);

        Roles::create($data);

        return redirect()->back()->with('success', 'Rol creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $rol = Roles::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:roles,nombre,' . $rol->id,
            'descripcion' => 'nullable|string|max:255',
            'activo' => 'required|boolean',
        ]);

        $rol->update($data);

        return redirect()->back()->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy($id)
    {
        $rol = Roles::findOrFail($id);

        // Evitar borrar si hay contactos asociados
        if ($rol->contactos()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar: este rol está asignado a contactos.');
        }

        try {
            $rol->delete();
            return redirect()->back()->with('success', 'Rol eliminado correctamente.');
        } catch (QueryException $e) {
            // Por si existe alguna restricción en BD (foreign key, etc.)
            return redirect()->back()->with('error', 'No se pudo eliminar el rol. Está relacionado con otros registros.');
        }
    }
}
