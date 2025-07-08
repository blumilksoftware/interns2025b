<?php

declare(strict_types=1);

namespace Interns2025b\Policies;

use Interns2025b\Models\User;
use Interns2025b\Enums\Role;

class AdminPolicy
{
    public function view(User $authUser, User $targetUser): bool
    {
        dd($authUser->hasRole(Role::SuperAdministrator));
        return $authUser->hasRole(Role::SuperAdministrator->value) || $authUser->id === $targetUser->id;
    }

    public function create(User $authUser): bool
    {
        return $authUser->hasRole(Role::SuperAdministrator);
    }

    public function update(User $authUser, User $targetUser): bool
    {
        return $authUser->hasRole(Role::SuperAdministrator);
    }

    public function delete(User $authUser, User $targetUser): bool
    {
        return $authUser->hasRole(Role::SuperAdministrator->value);
    }
}
