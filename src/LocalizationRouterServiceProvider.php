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
        /*
            - locale cannot be a prefixed argument like {locale?} - because it breaks arguments order {locale}/{university}
            - foreach route generator every time overrides route name definition and all routes are generated with last locale
         */


        Route::macro('locales', function ($routes) {
            Route::prefix('{locale?}')
                // TODO: build where from allowed locales list config('app.locales')
                ->where(['locale' => '(en|ru)'])
                ->middleware(LocalizationMiddleware::class)
                ->group($routes);
        });
    }
}
