<?php

namespace Nevadskiy\LocalizeRoutes\Listeners;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Foundation\Events\LocaleUpdated;

class SwitchRouterLocale
{
    /**
     * @var UrlGenerator
     */
    private $generator;

    /**
     * SwitchLocale constructor.
     *
     * @param UrlGenerator $generator
     */
    public function __construct(UrlGenerator $generator)
    {
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
        $this->generator->defaults(['locale' => $event->locale]);
    }
}
