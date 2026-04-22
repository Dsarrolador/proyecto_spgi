<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMantenimiento
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
        abort_unless(auth()->user() && (auth()->user()->es_admin || auth()->user()->es_encargado), 403, 'No tienes permiso para acceder a Mantenimiento.');

        return $next($request);
    }
}
