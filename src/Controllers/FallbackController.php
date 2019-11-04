<?php

namespace Nevadskiy\LocalizationRouter\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Nevadskiy\LocalizationRouter\LocaleUrl;
use Nevadskiy\LocalizationRouter\Repositories\UserLocaleRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FallbackController extends Controller
{
    /**
     * @var LocaleUrl
     */
    private $localeUrl;

    /**
     * @var UserLocaleRepository
     */
    private $repository;

    /**
     * NotFoundHandler constructor.
     *
     * @param LocaleUrl $localeUrl
     * @param UserLocaleRepository $repository
     */
    public function __construct(LocaleUrl $localeUrl, UserLocaleRepository $repository)
    {
        $this->localeUrl = $localeUrl;
        $this->repository = $repository;
    }

    /**
     * It uses for web middleware loading and allow to use session, authentication and others useful data.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function __invoke(Request $request)
    {
        if ($this->localeUrl->isCorrectRequestLocale($request)) {
            throw new NotFoundHttpException;
        }

        $url = $this->localeUrl->prependLocale($this->pullLocale(), $request);

        if (! $this->localeUrl->isCorrect($url)) {
            throw new NotFoundHttpException;
        }

        return $this->redirectToLocaleUrl($url);
    }

    /**
     * Pull the user locale.
     *
     * @return string
     */
    private function pullLocale(): string
    {
        $locale = $this->repository->get();

        app()->setLocale($locale);

        return $locale;
    }

    /**
     * Redirect to the locale URL.
     *
     * @param string $url
     * @return RedirectResponse
     */
    private function redirectToLocaleUrl(string $url): RedirectResponse
    {
        return redirect()->to($url, Response::HTTP_MOVED_PERMANENTLY, ['Vary' => 'Accept-Language']);
    }
}
