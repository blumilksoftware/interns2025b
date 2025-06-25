<?php

declare(strict_types=1);

namespace Interns2025b\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Interns2025b\Events\EventStartingSoon;
use Interns2025b\Events\EventWasCanceled;
use Interns2025b\Events\EventWasPublished;
use Interns2025b\Listeners\SendEventCanceledNotification;
use Interns2025b\Listeners\SendEventStartingSoonNotification;
use Interns2025b\Listeners\SendNewEventPublishedNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        EventStartingSoon::class => [
            SendEventStartingSoonNotification::class,
        ],
        EventWasPublished::class => [
            SendNewEventPublishedNotification::class,
        ],
        EventWasCanceled::class => [
            SendEventCanceledNotification::class,
        ],
    ];
}
