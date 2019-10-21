<?php

namespace Nevadskiy\LocalizationRouter\Repositories;

use Illuminate\Contracts\Session\Session;

class SessionRepository implements Repository
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

    public function set(string $locale): void
    {
        $this->session->put('locale', $locale);
    }

    public function get(): string
    {
        return $this->session->get('locale', $this->defaultLocale);
    }

    public function getDefault(): string
    {
        return $this->defaultLocale;
    }
}
