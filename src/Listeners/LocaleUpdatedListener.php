<?php

namespace Nevadskiy\LocalizationRouter\Listeners;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Foundation\Events\LocaleUpdated;
use Nevadskiy\LocalizationRouter\Repositories\UserLocaleRepository;

class LocaleUpdatedListener
{
    /**
     * @var UserLocaleRepository
     */
    private $repository;

    /**
     * @var UrlGenerator
     */
    private $generator;

    /**
     * LocaleUpdatedListener constructor.
     *
     * @param UserLocaleRepository $repository
     * @param UrlGenerator $generator
     */
    public function __construct(UserLocaleRepository $repository, UrlGenerator $generator)
    {
        $this->repository = $repository;
        $this->generator = $generator;
    }

    /**
     * Handle the event.
     *
     * @param LocaleUpdated $event
     * @return void
     */
    public function handle(LocaleUpdated $event): void
    {
        $this->repository->set($event->locale);
        $this->setDefaultUrlLocale($event->locale);
    }

    /**
     * Set default URL locale to allow to generate all routes without passing a locale.
     * E.g. Instead of 'route('articles', ['locale' => app()->getLocale()])' now available just 'route('article')'.
     *
     * @param string $locale
     */
    private function setDefaultUrlLocale(string $locale): void
    {
        $this->generator->defaults(['locale' => $locale]);
    }
}
