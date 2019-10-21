<?php

namespace Nevadskiy\LocalizationRouter;

use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Nevadskiy\LocalizationRouter\Listeners\LocaleUpdatedListener;
use Nevadskiy\LocalizationRouter\Listeners\RouteMatchedListener;

class LocalizationRouterServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the package.
     *
     * @var array
     */
    protected $listen = [
        LocaleUpdated::class => LocaleUpdatedListener::class,
        RouteMatched::class => RouteMatchedListener::class,
    ];

    /**
     * Boot any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->bootRoutePatterns();
        $this->bootEvents();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerRouteMacro();
        $this->registerContainerBindings();
    }

    /**
     * Boot the route locale pattern.
     */
    private function bootRoutePatterns(): void
    {
        Route::pattern('locale', $this->getLocalePattern());
    }

    /**
     * Get the locale pattern.
     *
     * @return string
     */
    private function getLocalePattern(): string
    {
        $default = $this->app[Repositories\Repository::class]->getDefault();

        $locales = config('app.locales', [$default]);

        return '(' . implode('|', $locales) . ')';
    }

    /**
     * Boot the package events.
     */
    private function bootEvents(): void
    {
        foreach ($this->listen as $event => $listener) {
            $this->app['events']->listen($event, $listener);
        }
    }

    /**
     * Register the route macro.
     */
    private function registerRouteMacro(): void
    {
        Route::macro('locale', function ($routes) {
            Route::prefix('{locale}')->group($routes);
        });
    }

    /**
     * Register the container bindings.
     */
    private function registerContainerBindings(): void
    {
        $this->app->when(LocaleUrl::class)
            ->needs('$locales')
            ->give($this->app['config']['app']['locales']);

        $this->app->when(Repositories\SessionRepository::class)
            ->needs('$defaultLocale')
            ->give($this->app['config']['app']['fallback_locale']);

        $this->app->bind(Repositories\Repository::class, Repositories\SessionRepository::class);
    }
}
