<?php

declare(strict_types=1);

namespace Interns2025b\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Interns2025b\Events\EventStartingSoon;
use Interns2025b\Events\EventWasCanceled;
use Interns2025b\Events\EventWasPublished;
use Interns2025b\Listeners\SendEventNotification;
use Interns2025b\Notifications\EventCanceledNotification;
use Interns2025b\Notifications\EventStartingSoonNotification;
use Interns2025b\Notifications\NewEventPublishedNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        EventWasCanceled::class => [SendEventNotification::class],
        EventWasPublished::class => [SendEventNotification::class],
        EventStartingSoon::class => [SendEventNotification::class],
    ];

    public function boot(): void
    {
        parent::boot();

        $this->app->when(SendEventNotification::class)
            ->needs("\$map")
            ->give([
                EventWasCanceled::class => EventCanceledNotification::class,
                EventWasPublished::class => NewEventPublishedNotification::class,
                EventStartingSoon::class => EventStartingSoonNotification::class,
            ]);
    }
}
