<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register middleware alias 'role' so routes can use ->middleware('role:pelatih') etc.
        $router = $this->app['router'];
        if ($router) {
            $router->aliasMiddleware('role', \App\Http\Middleware\CheckRole::class);
        }
        Paginator::defaultView('vendor.pagination.bmc');
    }
}
