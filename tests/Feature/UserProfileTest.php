<?php

declare(strict_types=1);

namespace Tests\Feature;

use Interns2025b\Models\Event;
use Interns2025b\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function testUserCanViewTheirProfile(): void
    {
        Sanctum::actingAs($this->user);

        $this->user->update([
            "facebook_id" => "1234567890",
        ]);

        $response = $this->getJson("/api/profile");

        $response->assertOk()
            ->assertJson([
                "message" => "User profile retrieved successfully.",
                "data" => [
                    "first_name" => $this->user->first_name,
                    "last_name" => $this->user->last_name,
                    "email" => $this->user->email,
                    "facebook_linked" => true,
                ],
            ]);
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
            ->assertJsonPath("data.last_name", "Name");
    }

    public function testUpdateProfileRequiresFirstName(): void
    {
        $this->actingAs($this->user)
            ->putJson("/api/profile", [
                "last_name" => "OnlyLastName",
            ])
            ->assertJsonValidationErrors("first_name");
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
            ->assertJson([
                "message" => "profile.redirected",
                "redirect" => "/api/profile",
            ]);
    }

    public function testAuthenticatedUserCanViewOtherUsersProfile(): void
    {
        $otherUser = User::factory()->create();

        $this->actingAs($this->user)
            ->getJson("/api/profile/{$otherUser->id}")
            ->assertOk()
            ->assertJsonPath("data.id", $otherUser->id);
    }

    public function testUserDetailProfileContainsCountsAndEvents(): void
    {
        $targetUser = User::factory()->create();
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
                    "events",
                    "events_count",
                    "followers_count",
                    "following_count",
                ],
            ]);
    }
}
