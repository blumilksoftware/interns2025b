<?php

declare(strict_types=1);

namespace Interns2025b\Console\Commands;

use Illuminate\Console\Command;
use Interns2025b\Enums\EventStatus;
use Interns2025b\Models\Event;

class UpdateEventStatuses extends Command
{
    protected $signature = "events:update-statuses";

    public function handle(): int
    {
        Event::query()->where("status", EventStatus::Published)
            ->whereNotNull("start")
            ->where("start", "<=", now())
            ->update(["status" => EventStatus::Ongoing]);

        Event::query()->where("status", EventStatus::Ongoing)
            ->whereNotNull("end")
            ->where("end", "<=", now())
            ->update(["status" => EventStatus::Ended]);

        $this->info("Event statuses updated.");

        return 0;
    }
}
