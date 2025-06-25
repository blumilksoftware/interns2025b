<?php

declare(strict_types=1);

namespace Interns2025b\Console\Commands;

use Illuminate\Console\Command;
use Interns2025b\Events\EventWasCanceled;
use Interns2025b\Models\Event;

class NotifyCanceledEventsCommand extends Command
{
    protected $signature = "followers:notify-canceled";

    public function handle(): int
    {
        $events = Event::where("status", "canceled")
            ->whereDate("updated_at", today())
            ->get();

        foreach ($events as $event) {
            foreach ($event->participants as $user) {
                event(new EventWasCanceled($event, $user));
            }
        }

        $this->info("EventWasCanceled dispatched.");

        return self::SUCCESS;
    }
}
