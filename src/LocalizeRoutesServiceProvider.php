<?php

namespace Nevadskiy\LocalizeRoutes;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Nevadskiy\LocalizeRoutes\Controllers\FallbackController;
use Nevadskiy\LocalizeRoutes\Middleware\SetLocaleMiddleware;

class LocalizeRoutesServiceProvider extends ServiceProvider
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
        $this->registerRouteMacros();
        $this->registerContainerBindings();
    }

    /**
     * Boot any package services.
     */
    public function boot(): void
    {
        $this->setDefaultLocale();
        $this->bootEvents();
        $this->bootRoutes();
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
        return '(' . implode('|', $this->app['config']['app']['locales']) . ')';
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
     * Set default locale.
     */
    private function setDefaultLocale(): void
    {
        $this->app[UrlGenerator::class]->defaults(['locale' => $this->app->getLocale()]);

        $this->app->rebinding(UrlGenerator::class, function ($url) {
            $url->defaults(['locale' => $this->app->getLocale()]);
        });
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

    /**
     * Boot any application routes.
     */
    private function bootRoutes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        $router = $this->resolveRouter();

        $router->group([
            'middleware' => 'web',
        ], function () use ($router) {
            $router->fallback(FallbackController::class);
        });
    }

    /**
     * Resolve a router.
     *
     * @return Router
     */
    private function resolveRouter(): Router
    {
        return $this->app['router'];
    }
}
