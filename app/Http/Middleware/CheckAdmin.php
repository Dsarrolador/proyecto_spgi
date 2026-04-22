<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdmin
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
        abort_unless(auth()->user() && auth()->user()->es_admin, 403, 'Solo el Administrador tiene acceso al módulo de Usuarios.');

        return $next($request);
    }
}
