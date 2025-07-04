<?php

declare(strict_types=1);

namespace Interns2025b\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Interns2025b\Models\Event;
use Interns2025b\Models\User;

abstract class BaseEventNotificationEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Event $event,
        public readonly User $user,
    ) {}
}
