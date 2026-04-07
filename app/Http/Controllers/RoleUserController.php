<?php

namespace App\Http\Controllers;

use App\Models\RoleUser;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class RoleUserController extends Controller
{
    public function index()
    {
        $roles = RoleUser::orderBy('nombre')->get();
        return view('mantenimiento.roles_usuario.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:role_user,nombre',
        ]);

        RoleUser::create($data);

        return redirect()->back()->with('success', 'Rol de usuario creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $rol = RoleUser::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:role_user,nombre,' . $rol->id,
        ]);

        $rol->update($data);

        return redirect()->back()->with('success', 'Rol de usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $rol = RoleUser::findOrFail($id);

        // Verificar si hay usuarios asociados
        if ($rol->users()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar: este rol está asignado a usuarios del sistema.');
        }

        try {
            $rol->delete();
            return redirect()->back()->with('success', 'Rol de usuario eliminado correctamente.');
        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'No se pudo eliminar el rol. Está relacionado con otros registros.');
        }
    }
}
