<?php

declare(strict_types=1);

namespace Interns2025b\Policies;

use Interns2025b\Enums\Role;
use Interns2025b\Models\User;

class UserPolicy
{
    public function view(User $user, User $targetUser): bool
    {
        return $user->hasRole(Role::SuperAdministrator->value) || $user->id === $targetUser->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::SuperAdministrator);
    }

    public function update(User $user, User $targetUser): bool
    {
        return $user->hasRole(Role::SuperAdministrator);
    }

    public function delete(User $user, User $targetUser): bool
    {
        return $user->hasRole(Role::SuperAdministrator->value);
    }
}
