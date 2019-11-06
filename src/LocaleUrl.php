<?php

namespace Nevadskiy\LocalizationRouter;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LocaleUrl
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var array
     */
    private $locales;

    /**
     * @var UrlGenerator
     */
    private $generator;

    /**
     * LocaleUrl constructor.
     *
     * @param UrlGenerator $generator
     * @param Request $request
     * @param array $locales
     */
    public function __construct(UrlGenerator $generator, Request $request, array $locales)
    {
        $this->generator = $generator;
        $this->request = $request;
        $this->locales = $locales;
    }

    /**
     * Get available locales.
     *
     * @return array
     */
    public function getLocales(): array
    {
        return $this->locales;
    }

    /**
     * Get preferred locale from the request.
     *
     * @param array $extendedLocales
     * @param Request|null $request
     * @return string|null
     */
    public function getPreferred(array $extendedLocales = [], Request $request = null): ?string
    {
        $request = $request ?: $this->request;

        return $request->getPreferredLanguage(array_merge($extendedLocales, $this->locales));
    }

    /**
     * Generate previous URI with the given locale.
     *
     * @param string $locale
     * @return string
     */
    public function previousWithLocale(string $locale): string
    {
        $previous = $this->generator->previous();

        $previousRequest = Request::create($previous);

        if (! $this->hasCorrectRequestLocale($previousRequest)) {
            return $previous;
        }

        return $this->replaceUrlLocale($locale, $previousRequest);
    }

    /**
     * Prepend the locale to the request URL.
     *
     * @param string $locale
     * @param Request|null $request
     * @return string
     */
    public function prependLocale(string $locale, Request $request = null): string
    {
        $this->guardInvalidLocale($locale);

        $request = $request ?: $this->request;

        return $this->generator->to("{$locale}{$request->getRequestUri()}");
    }

    /**
     * Determine if the request URL has matched any application route.
     *
     * @param string $url
     * @return bool
     */
    public function isCorrect(string $url): bool
    {
        try {
            $route = app('router')->getRoutes()->match(Request::create($url));
        } catch (NotFoundHttpException $e) {
            return false;
        }

        if ($route->isFallback) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the request has a correct locale.
     *
     * @param Request|null $request
     * @return bool
     */
    public function hasCorrectRequestLocale(Request $request = null): bool
    {
        $request = $request ?: $this->request;

        $locale = $request->segment(1);

        return $locale && $this->isCorrectLocale($locale);
    }

    /**
     * Replace locale in the request URL.
     *
     * @param string $locale
     * @param Request $request
     * @return string
     */
    private function replaceUrlLocale(string $locale, Request $request = null): string
    {
        $request = $request ?: $this->request;

        $uri =  preg_replace("/{$request->segment(1)}/", $locale, $request->getRequestUri(), 1);

        return $this->generator->to($uri);
    }

    /**
     * Guard against an invalid application locale.
     *
     * @param string $locale
     */
    private function guardInvalidLocale(string $locale): void
    {
        if (!$this->isCorrectLocale($locale)) {
            throw new InvalidArgumentException("The locale '{$locale}' is not available in the app.");
        }
    }

    /**
     * Determine if the locale is correct.
     *
     * @param string $locale
     * @return bool
     */
    private function isCorrectLocale(string $locale): bool
    {
        return \in_array($locale, $this->locales, true);
    }
}
