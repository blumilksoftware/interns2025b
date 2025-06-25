<?php

declare(strict_types=1);

namespace Interns2025b\Listeners;

use Interns2025b\Events\EventWasPublished;
use Interns2025b\Notifications\NewEventPublishedNotification;

class SendNewEventPublishedNotification
{
    public function handle(EventWasPublished $event): void
    {
        $event->user->notify(new NewEventPublishedNotification($event->event));

        activity()
            ->performedOn($event->event)
            ->withProperties(["user_id" => $event->user->id])
            ->log("Sent NewEventPublishedNotification");
    }
}
