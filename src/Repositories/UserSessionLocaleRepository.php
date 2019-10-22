<?php

namespace Nevadskiy\LocalizationRouter\Repositories;

use Illuminate\Contracts\Session\Session;

class UserSessionLocaleRepository implements UserLocaleRepository
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * SessionRepository constructor.
     *
     * @param Session $session
     * @param string $defaultLocale
     */
    public function __construct(Session $session, string $defaultLocale)
    {
        $this->session = $session;
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
        return $this->session->get('locale', $this->defaultLocale);
    }
}
