<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Fix for missing FTP extension constants
        if (!defined('FTP_ASCII'))  define('FTP_ASCII', 1);
        if (!defined('FTP_BINARY')) define('FTP_BINARY', 2);

        \Illuminate\Pagination\Paginator::useBootstrap();

        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            $today = \Carbon\Carbon::today();
            $upcomingBirthdays = \App\Models\User::with('cumpleanos')->whereHas('cumpleanos')->get()->filter(function($user) use ($today) {
                $fecha_nacimiento = \Carbon\Carbon::parse($user->cumpleanos->fecha_nacimiento);
                $cumpleanosEsteAno = \Carbon\Carbon::create($today->year, $fecha_nacimiento->month, $fecha_nacimiento->day);
                // Mostrar si es hoy o en los proximos 7 dias
                return $cumpleanosEsteAno->between($today, $today->copy()->addDays(7));
            })->sortBy(function($user) use ($today) {
                $fecha_nacimiento = \Carbon\Carbon::parse($user->cumpleanos->fecha_nacimiento);
                return \Carbon\Carbon::create($today->year, $fecha_nacimiento->month, $fecha_nacimiento->day)->timestamp;
            })->map(function($user) {
                return (object)[
                    'nombre' => $user->name,
                    'fecha_nacimiento' => $user->cumpleanos->fecha_nacimiento,
                ];
            })->take(5);

            $view->with('upcomingBirthdays', $upcomingBirthdays);
        });
    }

}
