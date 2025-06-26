<?php

declare(strict_types=1);

namespace Interns2025b\Listeners;

use Interns2025b\Events\BaseEventNotificationEvent;

class SendEventNotification
{
    public function __construct(
        protected array $map = [],
    ) {}

    public function handle(BaseEventNotificationEvent $event): void
    {
        $notificationClass = $this->map[$event::class] ?? null;

        if (!$notificationClass) {
            return;
        }

        $event->user->notify(new $notificationClass($event->event));
    }
}
