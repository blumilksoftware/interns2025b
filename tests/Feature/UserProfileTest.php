<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Interns2025b\Models\User;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
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

    public function testUserRedirectedToProfileIfTheyAccessTheirOwnId(): void
    {
        $this->actingAs($this->user)
            ->get("/api/profile/{$this->user->id}")
            ->assertRedirect("/api/profile");
    }

    public function testAuthenticatedUserCanViewOtherUsersProfile(): void
    {
        $otherUser = User::factory()->create();

        $this->actingAs($this->user)
            ->getJson("/api/profile/{$otherUser->id}")
            ->assertOk()
            ->assertJsonPath("data.id", $otherUser->id);
    }
}
