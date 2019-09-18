<?php

namespace Nevadskiy\LocalizationRouter;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Nevadskiy\LocalizationRouter\Middleware\LocalizationMiddleware;

class LocalizationRouterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function register(): void
    {
        Route::macro('locales', function ($routes) {
            Route::prefix('{locale?}')->middleware(LocalizationMiddleware::class)->group($routes);
        });
    }
}
