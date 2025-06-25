<?php

declare(strict_types=1);

namespace Interns2025b\Observers;

use Interns2025b\Events\EventWasCanceled;
use Interns2025b\Models\Event;

class EventObserver
{
    public function updated(Event $event): void
    {
        if ($event->wasChanged("status") && $event->status === "canceled") {
            foreach ($event->participants as $user) {
                event(new EventWasCanceled($event, $user));
            }
        }
    }
}
