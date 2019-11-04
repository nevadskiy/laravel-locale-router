<?php

namespace Nevadskiy\LocalizationRouter;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Nevadskiy\LocalizationRouter\Providers;

class LocalizationRouterServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->registerProviders();
        $this->registerRouteMacros();
        $this->registerContainerBindings();
    }

    /**
     * Register any package providers.
     */
    private function registerProviders(): void
    {
        // TODO: refactor without providers...
        $this->app->register(Providers\EventServiceProvider::class);
        $this->app->register(Providers\RouteServiceProvider::class);
    }

    /**
     * Register any route macros.
     */
    private function registerRouteMacros(): void
    {
        $pattern = $this->getLocalePattern();

        Route::macro('locale', function ($routes) use ($pattern) {
            Route::prefix('{locale}')->where(['locale' => $pattern])->group($routes);
        });
    }

    /**
     * Get the locale pattern.
     *
     * @return string
     */
    private function getLocalePattern(): string
    {
        return '(' . implode('|', config('app.locales')) . ')';
    }

    /**
     * Register any container bindings.
     */
    private function registerContainerBindings(): void
    {
        $this->app->when(LocaleUrl::class)
            ->needs('$locales')
            ->give($this->app['config']['app']['locales']);

        $this->app->when(Repositories\UserSessionLocaleRepository::class)
            ->needs('$defaultLocale')
            ->give($this->app['config']['app']['fallback_locale']);

        $this->app->bind(Repositories\UserLocaleRepository::class, Repositories\UserSessionLocaleRepository::class);
    }
}
