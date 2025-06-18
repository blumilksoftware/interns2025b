<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Interns2025b\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class FacebookLoginTest extends TestCase
{
    use RefreshDatabase;

    public function testRedirectReturnsFacebookUrl(): void
    {
        $response = $this->getJson("/api/auth/facebook/redirect");
        $response->assertStatus(200);
        $this->assertArrayHasKey("url", $response->json());
    }

    public function testCallbackCreatesUserAndReturnsToken(): void
    {
        $this->mockSocialiteUser();

        $response = $this->getJson("/api/auth/facebook/callback");

        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "token", "user_id"]);
        $this->assertDatabaseHas("users", ["facebook_id" => "1234567890"]);
    }

    public function testCallbackReturnsExistingUser(): void
    {
        $this->mockSocialiteUser();

        $existingUser = User::factory()->create([
            "facebook_id" => "1234567890",
            "firstname" => "John",
        ]);

        $response = $this->getJson("/api/auth/facebook/callback");

        $response->assertStatus(200);
        $this->assertEquals($existingUser->id, $response->json("user_id"));
    }

    protected function mockSocialiteUser(array $overrides = []): void
    {
        $user = \Mockery::mock(\Laravel\Socialite\Contracts\User::class);
        $user->shouldReceive("getEmail")->andReturn("test@example.com");
        $user->shouldReceive("getId")->andReturn("1234567890");
        $user->shouldReceive("getName")->andReturn("Test User");

        $socialiteDriver = \Mockery::mock();
        $socialiteDriver->shouldReceive("stateless")->andReturnSelf();
        $socialiteDriver->shouldReceive("user")->andReturn($user);

        Socialite::shouldReceive("driver")
            ->with("facebook")
            ->andReturn($socialiteDriver);
    }
}
