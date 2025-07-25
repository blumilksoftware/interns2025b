<?php

declare(strict_types=1);

namespace Interns2025b\Policies;

use Interns2025b\Enums\Role;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;

class OrganizationPolicy
{
    public function view(User $user, Organization $organization): bool
    {
        return true;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::Administrator->value)
            || $user->hasRole(Role::SuperAdministrator->value);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::Administrator->value)
            || $user->hasRole(Role::SuperAdministrator->value);
    }

    public function update(User $user, Organization $organization): bool
    {
        return $organization->owner_id === $user->id
            || $user->hasRole(Role::Administrator->value)
            || $user->hasRole(Role::SuperAdministrator->value);
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $organization->owner_id === $user->id
            || $user->hasRole(Role::Administrator->value)
            || $user->hasRole(Role::SuperAdministrator->value);
    }

    public function invite(User $user, Organization $organization): bool
    {
        if ($organization->owner_id === $user->id) {
            return true;
        }

        return $user->hasRole(Role::Administrator->value)
            || $user->hasRole(Role::SuperAdministrator->value);
    }
}
