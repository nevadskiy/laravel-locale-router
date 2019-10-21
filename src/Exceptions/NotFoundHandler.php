<?php

namespace Nevadskiy\LocalizationRouter\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Nevadskiy\LocalizationRouter\LocaleUrl;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotFoundHandler
{
    /**
     * @var LocaleUrl
     */
    private $localeUrl;

    /**
     * NotFoundHandler constructor.
     *
     * @param LocaleUrl $localeUrl
     */
    public function __construct(LocaleUrl $localeUrl)
    {
        $this->localeUrl = $localeUrl;
    }

    /**
     * Prepare exception for rendering.
     *
     * @param Request $request
     * @param Exception $exception
     * @return Exception
     */
    public function prepareException($request, Exception $exception): Exception
    {
        $defaultLocale = config('app.fallback_locale');

        if (! $exception instanceof NotFoundHttpException) {
            return $exception;
        }

        // Helps If 404 view contains any locale links
        url()->defaults(['locale' => $defaultLocale]);

        if ($this->localeUrl->isCorrectRequestLocale($request)) {
            return $exception;
        }

        $url = $this->localeUrl->prependLocale($defaultLocale, $request);

        if (! $this->localeUrl->isCorrect($url)) {
            return $exception;
        }

        return NotFoundByWrongLocaleException::withUri($exception, $url);
    }
}
