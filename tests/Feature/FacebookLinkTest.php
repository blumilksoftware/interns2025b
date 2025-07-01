<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Interns2025b\Models\User;
use Laravel\Sanctum\Sanctum;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class FacebookLinkTest extends TestCase
{
    use RefreshDatabase;

    public function testCallbackLinksFacebookAccountSuccessfully(): void
    {
        $this->mockSocialiteUser(["id" => "new_fb_id"]);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/link/facebook/callback");

        $response->assertStatus(200);
        $response->assertJson(["message" => "Facebook account linked successfully"]);

        $this->assertDatabaseHas("users", [
            "id" => $user->id,
            "facebook_id" => "new_fb_id",
        ]);
    }

    public function testCallbackReturnsConflictIfFacebookIdLinkedToAnotherUser(): void
    {
        $existingUser = User::factory()->create(["facebook_id" => "existing_fb_id"]);
        $user = User::factory()->create();

        $this->mockSocialiteUser(["id" => "existing_fb_id"]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/link/facebook/callback");

        $response->assertStatus(409);
        $response->assertJson(["message" => "Facebook account already linked to another user"]);
    }

    protected function mockSocialiteUser(array $overrides = []): void
    {
        $user = \Mockery::mock(\Laravel\Socialite\Contracts\User::class);
        $user->shouldReceive("getId")->andReturn($overrides["id"] ?? "1234567890");
        $user->shouldReceive("getName")->andReturn($overrides["name"] ?? "John Doe");
        $user->shouldReceive("getEmail")->andReturn($overrides["email"] ?? "john@example.com");
        $user->shouldReceive("getAvatar")->andReturn($overrides["avatar"] ?? "http://example.com/avatar.jpg");

        $socialiteDriver = \Mockery::mock();
        $socialiteDriver->shouldReceive("stateless")->andReturnSelf();
        $socialiteDriver->shouldReceive("user")->andReturn($user);

        Socialite::shouldReceive("driver")
            ->with("facebook")
            ->andReturn($socialiteDriver);
    }
}
