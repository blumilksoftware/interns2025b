<?php

declare(strict_types=1);

namespace Interns2025b\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Interns2025b\Models\Event;
use Interns2025b\Models\User;

class EventStartingSoon
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Event $event,
        public User $user,
    ) {}
}
