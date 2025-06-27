<?php

declare(strict_types=1);

namespace Tests\Unit;

use Interns2025b\Actions\ToggleFollowAction;
use Interns2025b\Models\Event;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;
use InvalidArgumentException;
use Tests\TestCase;

class ToggleFollowActionTest extends TestCase
{
    private ToggleFollowAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new ToggleFollowAction();
    }

    public function testFollowAndUnfollowUser(): void
    {
        $user = User::factory()->create();
        $targetUser = User::factory()->create();

        $message = $this->action->execute($user, "user", $targetUser->id);
        $this->assertEquals(__("follow.followed"), $message);
        $this->assertTrue($user->followingUsers()->where("user_id", $targetUser->id)->exists());

        $message = $this->action->execute($user, "user", $targetUser->id);
        $this->assertEquals(__("follow.unfollowed"), $message);
        $this->assertFalse($user->followingUsers()->where("user_id", $targetUser->id)->exists());
    }

    public function testCannotFollowSelf(): void
    {
        $user = User::factory()->create();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__("follow.cannot_follow_self"));

        $this->action->execute($user, "user", $user->id);
    }

    public function testInvalidFollowTypeThrowsException(): void
    {
        $user = User::factory()->create();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__("follow.invalid_follow_type"));

        $this->action->execute($user, "invalid_type", 1);
    }

    public function testFollowNonexistentIdThrowsException(): void
    {
        $user = User::factory()->create();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(__("follow.error"));

        $this->action->execute($user, "user", 999999);
    }

    public function testFollowAndUnfollowOrganization(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $message = $this->action->execute($user, "organization", $organization->id);
        $this->assertEquals(__("follow.followed"), $message);
        $this->assertTrue($user->followingOrganizations()->where("followable_id", $organization->id)->exists());

        $message = $this->action->execute($user, "organization", $organization->id);
        $this->assertEquals(__("follow.unfollowed"), $message);
        $this->assertFalse($user->followingOrganizations()->where("followable_id", $organization->id)->exists());
    }

    public function testFollowAndUnfollowEvent(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();

        $message = $this->action->execute($user, "event", $event->id);
        $this->assertEquals(__("follow.followed"), $message);
        $this->assertTrue($user->followingEvents()->where("followable_id", $event->id)->exists());

        $message = $this->action->execute($user, "event", $event->id);
        $this->assertEquals(__("follow.unfollowed"), $message);
        $this->assertFalse($user->followingEvents()->where("followable_id", $event->id)->exists());
    }
}
