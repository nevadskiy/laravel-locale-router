<?php

namespace Nevadskiy\LocalizationRouter\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Nevadskiy\LocalizationRouter\Repositories\UserLocaleRepository;

class SetLocaleMiddleware
{
    /**
     * @var UserLocaleRepository
     */
    private $repository;

    /**
     * SetLocaleMiddleware constructor.
     *
     * @param UserLocaleRepository $repository
     */
    public function __construct(UserLocaleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();

        $locale = $this->getRouteLocale($route);

        $this->forgetRouteLocale($route);

        app()->setLocale($locale);

        return $next($request);
    }

    /**
     * Get the locale from the route or the locale repository.
     *
     * @param Route $route
     * @return string
     */
    private function getRouteLocale(Route $route): string
    {
        return $route->parameter('locale') ?: $this->repository->get();
    }

    /**
     * Forget the locale parameter that allows to accept only needed route parameters in controllers.
     * E.g. Instead of 'function show($locale, $id)' we just can rid of $locale and left only $id 'function show($id)'.
     *
     * @param Route $route
     */
    private function forgetRouteLocale(Route $route): void
    {
        $route->forgetParameter('locale');
    }
}
