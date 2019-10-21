<?php

namespace Nevadskiy\LocalizationRouter;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LocalizationRouterServiceProvider extends ServiceProvider
{
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
    }

    /**
     * Boot route locale pattern.
     */
    private function bootRoutePatterns(): void
    {
        Route::pattern('locale', $this->getLocalePattern());
    }

    /**
     * Get locale pattern.
     *
     * @return string
     */
    private function getLocalePattern(): string
    {
        return '(' . implode('|', config('app.locales', [config('app.fallback_locale')])) . ')';
    }

    /**
     * Boot route events.
     */
    private function bootEvents(): void
    {
        Route::matched(function (RouteMatched $event) {
            $locale = $event->route->parameter('locale');

            $this->app->setLocale($locale);

            $this->setDefaultUrlLocale($locale);

            $this->forgetLocaleParameter($event);
        });
    }

    /**
     * Set default URL locale to allow to generate all routes without passing a locale.
     * E.g. Instead of 'route('articles', ['locale' => app()->getLocale()])' now available just 'route('article')'.
     *
     * @param string $locale
     */
    private function setDefaultUrlLocale(string $locale = null): void
    {
        app(UrlGenerator::class)->defaults(['locale' => $locale ?: 'en']);
    }

    /**
     * Forget the locale parameter that allows to accept only needed route parameters in controllers.
     * E.g. Instead of 'function show($locale, $id)' we just can rid of $locale and left only $id 'function show($id)'.
     *
     * @param RouteMatched $event
     */
    private function forgetLocaleParameter(RouteMatched $event): void
    {
        $event->route->forgetParameter('locale');
    }

    /**
     * Register route macro.
     */
    private function registerRouteMacro(): void
    {
        Route::macro('locale', function ($routes) {
            Route::prefix('{locale}')->group($routes);
        });
    }
}
