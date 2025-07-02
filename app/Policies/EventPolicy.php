<?php

declare(strict_types=1);

namespace Interns2025b\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Interns2025b\Models\Event;
use Interns2025b\Models\User;

class EventPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Event $event): bool
    {
        return $user->id === $event->owner_id
            && $user::class === $event->owner_type;
    }

    public function update(User $user, Event $event): bool
    {
        return $this->view($user, $event);
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->view($user, $event);
    }
}
