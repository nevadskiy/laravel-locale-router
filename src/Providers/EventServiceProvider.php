<?php

namespace Nevadskiy\LocalizationRouter\Providers;

use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Nevadskiy\LocalizationRouter\Listeners;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        LocaleUpdated::class => [
            Listeners\LocaleUpdatedListener::class,
        ],
    ];
}
