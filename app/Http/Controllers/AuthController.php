<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 👉 Muestra el formulario de login
    public function loginForm()
    {
        return view('auth.login');
    }

    // 👉 Procesa el login
    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required'
        ]);

        // autenticación usando name
        if (Auth::attempt([
            'name' => $request->name,
            'password' => $request->password
        ])) {

            // 🔥 Después del login → ir a página de bienvenida
            return redirect()->route('bienvenido');
        }

        return back()->with('error', 'Nombre o contraseña incorrectos');
    }

    // 👉 Cerrar sesión
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
