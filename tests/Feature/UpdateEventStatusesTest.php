<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Interns2025b\Enums\EventStatus;
use Interns2025b\Models\Event;
use Tests\TestCase;

class UpdateEventStatusesTest extends TestCase
{
    use RefreshDatabase;

    public function testItUpdatesEventStatusesCorrectly(): void
    {
        $now = Carbon::now();

        $publishedEvent = Event::factory()->create([
            "status" => EventStatus::Published,
            "start" => $now->copy()->subHour(),
        ]);

        $ongoingEvent = Event::factory()->create([
            "status" => EventStatus::Ongoing,
            "end" => $now->copy()->subMinute(),
        ]);

        $futureEvent = Event::factory()->create([
            "status" => EventStatus::Published,
            "start" => $now->copy()->addHour(),
        ]);

        $this->artisan("events:update-statuses")
            ->expectsOutput("Event statuses updated.")
            ->assertExitCode(0);

        $this->assertEquals(EventStatus::Ongoing, $publishedEvent->fresh()->status);
        $this->assertEquals(EventStatus::Ended, $ongoingEvent->fresh()->status);
        $this->assertEquals(EventStatus::Published, $futureEvent->fresh()->status);
    }
}
