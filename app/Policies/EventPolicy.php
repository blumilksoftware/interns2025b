<?php

declare(strict_types=1);

namespace Interns2025b\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Interns2025b\Enums\Role;
use Interns2025b\Models\Event;
use Interns2025b\Models\User;

class EventPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Event $event): bool
    {
        return $this->isAdmin($user) || $this->isOwner($user, $event);
    }

    public function update(User $user, Event $event): bool
    {
        return $this->view($user, $event);
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->view($user, $event);
    }

    private function isOwner(User $user, Event $event): bool
    {
        return $user->id === $event->owner_id && $user::class === $event->owner_type;
    }

    private function isAdmin(User $user): bool
    {
        return $user->hasRole(Role::Administrator->value) || $user->hasRole(Role::SuperAdministrator->value);
    }
}
