<?php

declare(strict_types=1);

namespace Interns2025b\Listeners;

use Interns2025b\Events\EventStartingSoon;
use Interns2025b\Notifications\EventStartingSoonNotification;

class SendEventStartingSoonNotification
{
    public function handle(EventStartingSoon $event): void
    {
        $event->user->notify(new EventStartingSoonNotification($event->event));

        activity()
            ->performedOn($event->event)
            ->withProperties(["user_id" => $event->user->id])
            ->log("Sent EventStartingSoonNotification");
    }
}
