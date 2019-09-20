<?php

namespace Nevadskiy\LocalizationRouter;

use Route;
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
            Route::prefix('{locale?}')
                // TODO: build where from allowed locales list config('app.locales')
                ->where(['locale' => '(en|ru)'])
                ->middleware(LocalizationMiddleware::class)
                ->group($routes);
        });
    }
}
