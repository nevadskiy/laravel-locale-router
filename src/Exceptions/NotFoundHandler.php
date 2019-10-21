<?php

namespace Nevadskiy\LocalizationRouter\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotFoundHandler
{
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

        // Helps If 404 view contains any links
        URL::defaults(['locale' => $defaultLocale]);

        if (! $exception instanceof NotFoundHttpException) {
            return $exception;
        }

        if ($this->isCorrectRequestLocale($request)) {
            return $exception;
        }

        $url = $this->generateUrlWithLocale($request, $defaultLocale);

        if (! $this->isCorrectUrl($url)) {
            return $exception;
        }

        return NotFoundByWrongLocaleException::withUrl($exception, $url);
    }

    /**
     * Is correct request locale.
     *
     * @param Request $request
     * @return bool
     */
    private function isCorrectRequestLocale($request): bool
    {
        return \in_array($request->segment(1), config('app.locales'), true);
    }

    /**
     * Is correct URL.
     *
     * @param string $uri
     * @return bool
     */
    private function isCorrectUrl(string $uri): bool
    {
        try {
            app('router')->getRoutes()->match(Request::create($uri));
        } catch (NotFoundHttpException $e) {
            return false;
        }

        return true;
    }

    /**
     * Generate url with correct locale.
     *
     * @param Request $request
     * @param string $defaultLocale
     * @return string
     */
    private function generateUrlWithLocale($request, string $defaultLocale): string
    {
        $path = $request->getPathInfo();

        $url = url()->to("{$defaultLocale}{$path}");

        return $url;
    }
}
