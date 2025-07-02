<?php

declare(strict_types=1);

namespace Interns2025b\Policies;

use Interns2025b\Models\Event;
use Interns2025b\Models\User;

class EventPolicy
{
    public function update(User $user, Event $event): bool
    {
        return $user->hasAnyRole(["admin", "superadmin"]) || $this->isOwner($user, $event);
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->hasAnyRole(["admin", "superadmin"]) || $this->isOwner($user, $event);
    }

    protected function isOwner(User $user, Event $event): bool
    {
        return $event->owner_type === get_class($user) && $event->owner_id === $user->id;
    }
}
