<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 👉 Muestra el formulario de login
    public function loginForm()
    {
        $lockout_until = session('lockout_until');
        $seconds_left = 0;
        if ($lockout_until && $lockout_until > now()->timestamp) {
            $seconds_left = $lockout_until - now()->timestamp;
        }
        return view('auth.login', compact('seconds_left'));
    }

    // 👉 Procesa el login
    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required'
        ]);

        $throttleKey = \Illuminate\Support\Str::lower($request->input('name')) . '|' . $request->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            session(['lockout_until' => now()->addSeconds($seconds)->timestamp]);
            $minutes = ceil($seconds / 60);
            return back()->with('error', "Demasiados intentos de acceso. Por favor, intente nuevamente en $minutes minutos.");
        }

        // autenticación usando name
        if (Auth::attempt([
            'name' => $request->name,
            'password' => $request->password
        ])) {
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
            // 🔥 Después del login → ir a página de selección
            return redirect()->route('seleccion');
        }

        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 300); // 5 minutes = 300 seconds

        return back()->with('error', 'Los datos ingresados no son correctos, por favor verifique su información e intente nuevamente');
    }

    // 👉 Cerrar sesión
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
