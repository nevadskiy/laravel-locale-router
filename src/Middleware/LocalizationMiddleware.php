<?php

namespace Nevadskiy\LocalizationRouter\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use URL;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->route('locale');

        // TODO: if locale is incorrect, for example: news, but it does not exist - should throw 404

        if (!$locale || !$this->isValidLocale($locale)) {
            return $this->redirectWithLocale($request);
        }

        $this->handleLocale($request, $locale);

        return $next($request);
    }

    /**
     * Redirect to current route with locale included
     *
     * @param Request $request
     * @return RedirectResponse
     */
    private function redirectWithLocale(Request $request): RedirectResponse
    {
        return redirect()->route(
            $request->route()->getName(),
            ['locale' => app()->getLocale()],
            Response::HTTP_FOUND,
            ['Vary' => 'Accept-Language']
        );
    }

    /**
     * Determine if the locale is valid
     *
     * @param $locale
     * @return bool
     */
    private function isValidLocale(string $locale): bool
    {
        return in_array($locale, config('app.locales'), true);
    }

    /**
     * Set application local
     *
     * @param Request $request
     * @param string $locale
     */
    private function handleLocale(Request $request, string $locale): void
    {
        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);
        $request->route()->forgetParameter('locale');
    }
}
