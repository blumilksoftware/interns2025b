<?php

declare(strict_types=1);

namespace Interns2025b\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Interns2025b\Enums\Role;
use Interns2025b\Models\Event;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;

class EventPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Event $event): bool
    {
        if ($user->hasRole(Role::Administrator->value) || $user->hasRole(Role::SuperAdministrator->value)) {
            return true;
        }

        if ($user::class === $event->owner_type && $user->id === $event->owner_id) {
            return true;
        }

        if ($event->owner_type === Organization::class &&
            $user->organizations()->where("organizations.id", $event->owner_id)->exists()) {
            return true;
        }

        return false;
    }

    public function update(User $user, Event $event): bool
    {
        return $this->view($user, $event);
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->view($user, $event);
    }

    public function create(User $user, Organization $organization): bool
    {
        return $organization->users()->where("users.id", $user->id)->exists();
    }
}
