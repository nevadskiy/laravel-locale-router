<?php

namespace Nevadskiy\LocalizationRouter\Listeners;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Routing\Events\RouteMatched;
use Nevadskiy\LocalizationRouter\Repositories\Repository;

class RouteMatchedListener
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * LocaleUpdatedListener constructor.
     *
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handle the event.
     *
     * @param RouteMatched $event
     * @return void
     */
    public function handle(RouteMatched $event): void
    {
        $locale = $event->route->parameter('locale');

        $this->forgetLocaleParameter($event);

        $this->setAppLocale($locale);

        $this->setDefaultUrlLocale();
    }

    /**
     * Set default URL locale to allow to generate all routes without passing a locale.
     * E.g. Instead of 'route('articles', ['locale' => app()->getLocale()])' now available just 'route('article')'.
     *
     * @param string $locale
     */
    private function setAppLocale(?string $locale): void
    {
        if ($locale) {
            app()->setLocale($locale);
        }
    }

    /**
     * Set default URL locale to allow to generate all routes without passing a locale.
     * E.g. Instead of 'route('articles', ['locale' => app()->getLocale()])' now available just 'route('article')'.
     */
    private function setDefaultUrlLocale(): void
    {
        app(UrlGenerator::class)->defaults(['locale' => $this->repository->getDefault()]);
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
}
