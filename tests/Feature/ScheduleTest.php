<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    public function testEventsUpdateStatusesCommandIsScheduled(): void
    {
        $schedule = app(Schedule::class);

        $found = collect($schedule->events())
            ->contains(
                fn($event) => str_contains($event->command, "events:update-statuses") &&
                $event->expression === "*/30 * * * *",
            );

        $this->assertTrue($found);
    }
}
