<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminOrEncargado
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Solo Administracion (1) y Encargado (2) tienen acceso
        if (Auth::check() && (Auth::user()->es_admin || Auth::user()->es_encargado)) {
            return $next($request);
        }

        abort(403, 'Acceso restringido al área Comercial.');
    }
}
