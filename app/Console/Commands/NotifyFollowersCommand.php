<?php

declare(strict_types=1);

namespace Interns2025b\Console\Commands;

use Illuminate\Console\Command;
use Interns2025b\Events\EventStartingSoon;
use Interns2025b\Events\EventWasPublished;
use Interns2025b\Models\Event;

class NotifyFollowersCommand extends Command
{
    protected $signature = "followers:notify";

    public function handle(): int
    {
        $this->dispatchEventStartingSoon();
        $this->dispatchNewlyPublishedEvents();

        return self::SUCCESS;
    }

    protected function dispatchEventStartingSoon(): void
    {
        $events = Event::where("status", "published")
            ->whereBetween("start", [now(), now()->addDay()])
            ->get();

        foreach ($events as $event) {
            foreach ($event->followers as $user) {
                event(new EventStartingSoon($event, $user));
            }
            activity()
                ->performedOn($event)
                ->withProperties(["followers_count" => $event->followers->count()])
                ->log("EventStartingSoon notifications sent for event '$event->title'");
        }

        $this->info("EventStartingSoon dispatched.");
    }

    protected function dispatchNewlyPublishedEvents(): void
    {
        $events = Event::where("status", "published")
            ->whereDate("created_at", today())
            ->get();

        foreach ($events as $event) {
            $owner = $event->owner;

            if (method_exists($owner, "followers")) {
                foreach ($owner->followers as $user) {
                    event(new EventWasPublished($event, $user));
                }
                activity()
                    ->performedOn($event)
                    ->withProperties(["followers_count" => $owner->followers->count()])
                    ->log("NewEventPublished notifications sent for event '$event->title'");
            }
        }

        $this->info("EventWasPublished dispatched.");
    }
}
