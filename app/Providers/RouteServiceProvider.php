<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     */
    public function boot()
    {

        Route::middleware('web')
            ->prefix('admin')
            ->group(base_path('routes/admin.php'));
    }
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }
}
