<?php

namespace Nevadskiy\LocalizationRouter;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Nevadskiy\LocalizationRouter\Middleware\SetLocaleMiddleware;
use Nevadskiy\LocalizationRouter\Providers;

class LocalizationRouterServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        LocaleUpdated::class => [
            Listeners\SwitchRouterLocale::class,
            Listeners\RememberLocale::class,
        ],
    ];

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
     * Boot any package services.
     */
    public function boot(): void
    {
        $this->bootEvents();
    }

    /**
     * Register any package providers.
     */
    private function registerProviders(): void
    {
        // TODO: refactor without providers...
        $this->app->register(Providers\RouteServiceProvider::class);
    }

    /**
     * Register any route macros.
     */
    private function registerRouteMacros(): void
    {
        $pattern = $this->getLocalePattern();

        Route::macro('locale', function ($routes) use ($pattern) {
            Route::prefix('{locale}')
                ->middleware(SetLocaleMiddleware::class)
                ->where(['locale' => $pattern])
                ->group($routes);
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

        $this->app->bindIf(Repositories\UserLocaleRepository::class, Repositories\UserSessionLocaleRepository::class);
    }

    /**
     * Boot any application events.
     */
    private function bootEvents(): void
    {
        $dispatcher = $this->resolveEventDispatcher();

        foreach ($this->listen as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                $dispatcher->listen($event, $listener);
            }
        }
    }

    /**
     * Resolve an event dispatcher.
     *
     * @return Dispatcher
     */
    private function resolveEventDispatcher(): Dispatcher
    {
        return $this->app[Dispatcher::class];
    }
}
