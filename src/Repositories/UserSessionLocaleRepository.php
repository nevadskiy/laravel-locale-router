<?php

namespace Nevadskiy\LocalizeRoutes\Repositories;

use Illuminate\Contracts\Session\Session;
use Nevadskiy\LocalizeRoutes\LocaleUrl;

class UserSessionLocaleRepository implements UserLocaleRepository
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var LocaleUrl
     */
    private $localeUrl;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * SessionRepository constructor.
     *
     * @param Session $session
     * @param LocaleUrl $localeUrl
     * @param string $defaultLocale
     */
    public function __construct(Session $session, LocaleUrl $localeUrl, string $defaultLocale)
    {
        $this->session = $session;
        $this->localeUrl = $localeUrl;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * Set the locale to the repository.
     *
     * @param string $locale
     */
    public function set(string $locale): void
    {
        $this->session->put('locale', $locale);
    }

    /**
     * Get the locale from the repository.
     *
     * @return string
     */
    public function get(): string
    {
        return $this->session->get('locale', $this->getFallback());
    }

    /**
     * Get the fallback locale.
     *
     * @return string
     */
    private function getFallback(): string
    {
        return $this->localeUrl->getPreferred() ?: $this->defaultLocale;
    }
}
