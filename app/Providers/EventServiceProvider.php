<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        // Auth Events
        Event::listen(function (\Illuminate\Auth\Events\Login $event) {
            \App\Services\AuditService::log('login', 'Inicio de sesión exitoso', 'Seguridad', null, $event->user);
        });

        Event::listen(function (\Illuminate\Auth\Events\Logout $event) {
            if ($event->user) {
                \App\Services\AuditService::log('logout', 'Cierre de sesión', 'Seguridad', null, $event->user);
            }
        });

        Event::listen(function (\Illuminate\Auth\Events\Failed $event) {
            \App\Services\AuditService::log('failed_login', 'Intento fallido de inicio de sesión', 'Seguridad', null, $event->user);
        });

        // Register Observers for important models
        $modelsToObserve = [
            \App\Models\User::class,
            \App\Models\ClienteMaestro::class,
            \App\Models\RequerimientoCliente::class,
            \App\Models\Lead::class,
            \App\Models\Proyecto::class,
            \App\Models\RequerimientoProyecto::class,
            \App\Models\Roles::class,
            \App\Models\WikiDocument::class,
        ];

        foreach ($modelsToObserve as $model) {
            $model::observe(\App\Observers\ModelAuditObserver::class);
        }
    }
}
