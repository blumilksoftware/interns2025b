<?php

declare(strict_types=1);

namespace Tests\Feature;

use Interns2025b\Models\Event;
use Interns2025b\Models\User;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            "avatar_url" => "https://example.com/original-avatar.jpg",
        ]);
    }

    public function testUserCanViewTheirProfile(): void
    {
        $this->actingAs($this->user);

        $this->user->update([
            "facebook_id" => "1234567890",
        ]);

        $response = $this->getJson("/api/profile");

        $response->assertOk()
            ->assertJsonPath("message", "User profile retrieved successfully.")
            ->assertJsonPath("data.first_name", $this->user->first_name)
            ->assertJsonPath("data.last_name", $this->user->last_name)
            ->assertJsonPath("data.email", $this->user->email)
            ->assertJsonPath("data.facebook_linked", true)
            ->assertJsonPath("data.avatar_url", $this->user->avatar_url);
    }

    public function testGuestCannotAccessProfile(): void
    {
        $this->getJson("/api/profile")
            ->assertUnauthorized();
    }

    public function testUserCanUpdateNameAndLastName(): void
    {
        $this->actingAs($this->user)
            ->putJson("/api/profile", [
                "first_name" => "Updated",
                "last_name" => "Name",
            ])
            ->assertOk()
            ->assertJsonPath("data.first_name", "Updated")
            ->assertJsonPath("data.last_name", "Name")
            ->assertJsonPath("data.avatar_url", $this->user->avatar_url);
    }

    public function testGuestCannotUpdateProfile(): void
    {
        $this->putJson("/api/profile", [
            "first_name" => "Nope",
            "last_name" => "StillNope",
        ])->assertUnauthorized();
    }

    public function testProfileUpdateDoesNotAcceptTooLongNames(): void
    {
        $longName = str_repeat("a", 300);

        $this->actingAs($this->user)
            ->putJson("/api/profile", [
                "first_name" => $longName,
                "last_name" => "Lastname",
            ])
            ->assertJsonValidationErrors("first_name");
    }

    public function testUserGetsSameResponseWhenAccessingOwnProfileById(): void
    {
        $this->actingAs($this->user);

        $response = $this->getJson("/api/profile/{$this->user->id}");

        $response->assertStatus(302)
            ->assertJsonPath("message", "profile.redirected")
            ->assertJsonPath("redirect", "/api/profile");
    }

    public function testAuthenticatedUserCanViewOtherUsersProfile(): void
    {
        $otherUser = User::factory()->create([
            "avatar_url" => "https://example.com/other-avatar.jpg",
        ]);

        $this->actingAs($this->user)
            ->getJson("/api/profile/{$otherUser->id}")
            ->assertOk()
            ->assertJsonPath("data.id", $otherUser->id)
            ->assertJsonPath("data.avatar_url", $otherUser->avatar_url);
    }

    public function testUserDetailProfileContainsCountsAndEvents(): void
    {
        $targetUser = User::factory()->create([
            "avatar_url" => "https://example.com/target-avatar.jpg",
        ]);

        Event::factory()->count(3)->create([
            "owner_id" => $targetUser->id,
            "owner_type" => User::class,
        ]);

        $follower = User::factory()->create();
        $follower->followingUsers()->attach($targetUser->id);
        $targetUser->followingUsers()->attach($follower->id);

        $this->actingAs($this->user);

        $response = $this->getJson("/api/profile/{$targetUser->id}");

        $response->assertOk()
            ->assertJsonPath("data.events_count", 3)
            ->assertJsonPath("data.followers_count", 1)
            ->assertJsonPath("data.following_count", 1)
            ->assertJsonStructure([
                "data" => [
                    "id",
                    "first_name",
                    "last_name",
                    "email",
                    "avatar_url",
                    "events",
                    "events_count",
                    "followers_count",
                    "following_count",
                ],
            ]);
    }

    public function testUserCanUpdateAvatarUrl(): void
    {
        $this->actingAs($this->user);

        $newAvatarUrl = "https://example.com/new-avatar.png";

        $response = $this->putJson("/api/profile", [
            "avatar_url" => $newAvatarUrl,
        ]);

        $response->assertOk()
            ->assertJsonPath("data.avatar_url", $newAvatarUrl);

        $this->assertDatabaseHas("users", [
            "id" => $this->user->id,
            "avatar_url" => $newAvatarUrl,
        ]);
    }

    public function testProfileUpdateFailsWithInvalidAvatarUrl(): void
    {
        $this->actingAs($this->user);

        $invalidAvatarUrl = "not-a-valid-url";

        $response = $this->putJson("/api/profile", [
            "avatar_url" => $invalidAvatarUrl,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors("avatar_url");
    }
}
