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
    }

}
