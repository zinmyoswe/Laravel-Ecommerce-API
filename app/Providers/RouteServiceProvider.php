<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, and other route configurations.
     */
    public function boot(): void
    {
        // Load routes/api.php with 'api' prefix and 'api' middleware
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));

        // Load routes/web.php with 'web' middleware
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }
}
