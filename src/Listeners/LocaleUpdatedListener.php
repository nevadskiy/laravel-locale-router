<?php

namespace Nevadskiy\LocalizationRouter\Listeners;

use Illuminate\Foundation\Events\LocaleUpdated;
use Nevadskiy\LocalizationRouter\Repositories\Repository;

class LocaleUpdatedListener
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * LocaleUpdatedListener constructor.
     *
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
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
