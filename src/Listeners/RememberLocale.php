<?php

namespace Nevadskiy\LocalizeRoutes\Listeners;

use Illuminate\Foundation\Events\LocaleUpdated;
use Nevadskiy\LocalizeRoutes\Repositories\UserLocaleRepository;

class RememberLocale
{
    /**
     * @var UserLocaleRepository
     */
    private $repository;

    /**
     * RememberLocale constructor.
     *
     * @param UserLocaleRepository $repository
     */
    public function __construct(UserLocaleRepository $repository)
    {
        $this->repository = $repository;
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
    }
}
