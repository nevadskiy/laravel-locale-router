<?php

namespace Nevadskiy\LocalizationRouter;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Routing\Events\RouteMatched;
use Route;
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
        Route::pattern('locale', $this->getLocalePattern());

        Route::matched(function (RouteMatched $event) {
            $locale = $event->route->parameter('locale');

            $this->app->setLocale($locale);

            app(UrlGenerator::class)->defaults(['locale' => $locale]);

            $event->route->forgetParameter('locale');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        Route::macro('locales', function ($routes) {
            Route::prefix('{locale}')
//                ->middleware(LocalizationMiddleware::class)
                ->group($routes);
        });
    }

    private function getLocalePattern(): string
    {
        return implode('|', config('app.locales'));
    }
}
