<?php

declare(strict_types=1);

namespace Tests\Feature;

use Interns2025b\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    public function user_can_view_their_profile(): void
    {
        $user = User::factory()->create([
            "facebook_id" => "1234567890",
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/profile");

        $response->assertOk()
            ->assertJson([
                "message" => "User profile retrieved successfully.",
                "data" => [
                    "first_name" => $user->first_name,
                    "last_name" => $user->last_name,
                    "email" => $user->email,
                    "facebook_linked" => true,
                ],
            ]);
    }

    public function user_profile_returns_false_for_facebook_if_not_linked(): void
    {
        $user = User::factory()->create([
            "facebook_id" => null,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/profile");

        $response->assertOk()
            ->assertJson([
                "data" => [
                    "facebook_linked" => false,
                ],
            ]);
    }

    public function testGuestCannotAccessProfile(): void
    {
        $response = $this->getJson("/api/profile");

        $response->assertUnauthorized();
    }

    public function testUserCanUpdateNameAndLastName(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $payload = [
            "first_name" => "UpdatedName",
            "last_name" => "UpdatedSurname",
        ];

        $response = $this->putJson("/api/profile", $payload);

        $response->assertOk()
            ->assertJson([
                "message" => "Profile updated successfully.",
                "data" => [
                    "first_name" => "UpdatedName",
                    "last_name" => "UpdatedSurname",
                    "email" => $user->email,
                ],
            ]);

        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "first_name" => "UpdatedName",
            "last_name" => "UpdatedSurname",
        ]);
    }

    public function testUpdateProfileRequiresFirstName(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/profile", [
            "last_name" => "OnlyLastName",
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(["first_name"]);
    }

    public function testGuestCannotUpdateProfile(): void
    {
        $response = $this->putJson("/api/profile", [
            "first_name" => "GuestName",
            "last_name" => "GuestLast",
        ]);

        $response->assertUnauthorized();
    }

    public function testProfileUpdateDoesNotAcceptTooLongNames(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson("/api/profile", [
            "first_name" => str_repeat("A", 300),
            "last_name" => str_repeat("B", 300),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(["first_name", "last_name"]);
    }
}
