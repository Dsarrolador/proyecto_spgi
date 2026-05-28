<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::with('cumpleanos')->get();
        $roles = \App\Models\RoleUser::all();
        return view('usuarios', compact('usuarios', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'cod_roleUser' => 'nullable|exists:role_user,id',
            'fecha_nacimiento' => 'nullable|date',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'cod_roleUser' => $request->cod_roleUser,
        ]);

        if ($request->filled('fecha_nacimiento')) {
            $user->cumpleanos()->create([
                'fecha_nacimiento' => $request->fecha_nacimiento,
            ]);
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'password' => 'nullable|confirmed|min:6',
            'cod_roleUser' => 'nullable|exists:role_user,id',
            'fecha_nacimiento' => 'nullable|date',
        ]);

        // Actualizar nombre y correo
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->cod_roleUser = $request->cod_roleUser;

        // Si se ingresó nueva contraseña, actualizarla
        if ($request->filled('password')) {
            $usuario->password = bcrypt($request->password);
        }

        $usuario->save();

        if ($request->filled('fecha_nacimiento')) {
            $usuario->cumpleanos()->updateOrCreate(
                ['user_id' => $usuario->id],
                ['fecha_nacimiento' => $request->fecha_nacimiento]
            );
        } else {
            $usuario->cumpleanos()->delete();
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente');
    }
}



