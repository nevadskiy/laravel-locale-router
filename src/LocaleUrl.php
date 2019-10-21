<?php

namespace Nevadskiy\LocalizationRouter;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
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
     * Get possible locales.
     *
     * @return array
     */
    public function getLocales(): array
    {
        return $this->locales;
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

        if (! $this->isCorrectRequestLocale($previousRequest)) {
            return $previous;
        }

        return $this->replaceUrlLocale($locale, $previousRequest);
    }

    /**
     * Replace locale in the request URL.
     *
     * @param string $locale
     * @param Request $request
     * @return string
     */
    public function replaceUrlLocale(string $locale, Request $request = null): string
    {
        $request = $request ?: $this->request;

        $uri =  preg_replace("/{$request->segment(1)}/", $locale, $request->getRequestUri(), 1);

        return $this->generator->to($uri);
    }

    /**
     * Prepend locale to the request URL.
     *
     * @param string $locale
     * @param Request|null $request
     * @return string
     */
    public function prependLocale(string $locale, Request $request = null): string
    {
        $request = $request ?: $this->request;

        return $this->generator->to("{$locale}{$request->getRequestUri()}");
    }

    /**
     * Determine if the request URL is correct.
     *
     * @param string $url
     * @return bool
     */
    public function isCorrect(string $url): bool
    {
        try {
            app('router')->getRoutes()->match(Request::create($url));
        } catch (NotFoundHttpException $e) {
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
    public function isCorrectRequestLocale(Request $request = null): bool
    {
        $request = $request ?: $this->request;

        $locale = $request->segment(1);

        return $locale && $this->isCorrectLocale($locale);
    }

    /**
     * Determine if the locale is correct.
     *
     * @param string $locale
     * @return bool
     */
    public function isCorrectLocale(string $locale): bool
    {
        return \in_array($locale, $this->locales, true);
    }
}
