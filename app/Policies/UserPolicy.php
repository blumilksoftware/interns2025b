<?php

declare(strict_types=1);

namespace Interns2025b\Policies;

use Interns2025b\Enums\Role;
use Interns2025b\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::Administrator->value);
    }

    public function view(User $user, User $targetUser): bool
    {
        return $user->hasRole(Role::Administrator->value) && $targetUser->hasRole(Role::User->value);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::Administrator->value);
    }

    public function update(User $user, User $targetUser): bool
    {
        return $user->hasRole(Role::Administrator->value) && $targetUser->hasRole(Role::User->value);
    }

    public function delete(User $user, User $targetUser): bool
    {
        return $user->hasRole(Role::Administrator->value) && $targetUser->hasRole(Role::User->value);
    }

    public function viewAnyAdmin(User $user): bool
    {
        return $user->hasRole(Role::SuperAdministrator->value);
    }

    public function viewAdmin(User $user, User $targetUser): bool
    {
        return $user->hasRole(Role::SuperAdministrator->value) && $targetUser->hasRole(Role::Administrator->value);
    }

    public function createAdmin(User $user): bool
    {
        return $user->hasRole(Role::SuperAdministrator->value);
    }

    public function updateAdmin(User $user, User $targetUser): bool
    {
        if ($user->id === $targetUser->id && $user->hasRole(Role::SuperAdministrator->value)) {
            return false;
        }

        return $user->hasRole(Role::SuperAdministrator->value) && $targetUser->hasRole(Role::Administrator->value);
    }

    public function deleteAdmin(User $user, User $targetUser): bool
    {
        if ($user->id === $targetUser->id) {
            return false;
        }

        return $user->hasRole(Role::SuperAdministrator->value) && $targetUser->hasRole(Role::Administrator->value);
    }
}
