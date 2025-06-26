<?php

declare(strict_types=1);

namespace Interns2025b\Actions;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;
use Interns2025b\Models\Event;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;
use InvalidArgumentException;

readonly class ToggleFollowAction
{
    public function execute(User $follower, string $type, int $id): string
    {
        $modelClass = $this->resolveFollowableClass($type);

        if (!class_exists($modelClass)) {
            throw new InvalidArgumentException(__("follow.invalid_follow_type"));
        }

        if ($modelClass === User::class && $follower->id === $id) {
            throw new InvalidArgumentException(__("follow.cannot_follow_self"));
        }

        if (!$modelClass::where("id", $id)->exists()) {
            throw new InvalidArgumentException(__("follow.error"));
        }

        $relation = $this->getFollowRelation($follower, $type);

        $attached = $relation->toggle($id);

        if (!empty($attached["attached"])) {
            return __("follow.followed");
        }

        if (!empty($attached["detached"])) {
            return __("follow.unfollowed");
        }

        return __("follow.error");
    }

    protected function resolveFollowableClass(string $type): string
    {
        return match (Str::lower($type)) {
            "user" => User::class,
            "organization" => Organization::class,
            "event" => Event::class,
            default => "",
        };
    }

    protected function getFollowRelation(User $user, string $type): MorphToMany
    {
        return match (Str::lower($type)) {
            "user" => $user->followingUsers(),
            "organization" => $user->followingOrganizations(),
            "event" => $user->followingEvents(),
            default => throw new InvalidArgumentException(__("follow.invalid_follow_type") . ": $type"),
        };
    }
}
