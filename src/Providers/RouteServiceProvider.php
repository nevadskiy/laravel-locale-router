<?php

namespace Nevadskiy\LocalizationRouter\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Nevadskiy\LocalizationRouter\Controllers\FallbackController;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapFallbackRoute();
    }

    /**
     * Map fallback route which has 'web' middleware group applied and session available.
     */
    private function mapFallbackRoute(): void
    {
        Route::middleware('web')->group(function () {
            Route::fallback(FallbackController::class);
        });
    }
}
