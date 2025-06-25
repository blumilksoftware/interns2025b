<?php

declare(strict_types=1);

namespace Interns2025b\Listeners;

use Interns2025b\Events\EventWasCanceled;
use Interns2025b\Notifications\EventCanceledNotification;

class SendEventCanceledNotification
{
    public function handle(EventWasCanceled $event): void
    {
        $event->user->notify(new EventCanceledNotification($event->event));

        activity()
            ->performedOn($event->event)
            ->withProperties(["user_id" => $event->user->id])
            ->log("Sent EventCanceledNotification");
    }
}
