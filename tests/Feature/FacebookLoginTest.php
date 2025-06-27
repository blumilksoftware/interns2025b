<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Interns2025b\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Tests\TestCase;
use Tests\Traits\MocksFacebookUser;

class FacebookLoginTest extends TestCase
{
    use RefreshDatabase;
    use MocksFacebookUser;

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
        $this->assertDatabaseHas("users", [
            "facebook_id" => "1234567890",
            "first_name" => "Test",
        ]);
    }

    public function testCallbackReturnsExistingUser(): void
    {
        $this->mockSocialiteUser();

        $existingUser = User::factory()->create([
            "facebook_id" => "1234567890",
            "first_name" => "Test",
            "last_name" => "User",
            "email" => "test@example.com",
        ]);

        $response = $this->getJson("/api/auth/facebook/callback");

        $response->assertStatus(200);
        $this->assertEquals($existingUser->id, $response->json("user_id"));
    }

    public function testCallbackFailsWhenFacebookEmailIsMissing(): void
    {
        $this->mockSocialiteUser(["email" => null]);

        $response = $this->getJson("/api/auth/facebook/callback");

        $response->assertStatus(422);
        $response->assertJson(["message" => __("auth.email_required_from_facebook")]);
    }

    public function testCallbackFailsWhenFacebookIdIsMissing(): void
    {
        $this->mockSocialiteUser(["id" => null]);

        $response = $this->getJson("/api/auth/facebook/callback");

        $response->assertStatus(422);
        $response->assertJson(["message" => __("auth.facebook_id_required")]);
    }

    public function testCallbackFailsWhenEmailAlreadyExistsWithoutFacebookId(): void
    {
        User::factory()->create([
            "email" => "test@example.com",
            "facebook_id" => null,
        ]);

        $this->mockSocialiteUser();

        $response = $this->getJson("/api/auth/facebook/callback");

        $response->assertStatus(403);
        $response->assertJson(["message" => __("auth.email_already_registered")]);
    }

    public function testCallbackHandlesFacebookException(): void
    {
        Socialite::shouldReceive("driver")
            ->with("facebook")
            ->andThrow(new InvalidStateException("Invalid state"));

        $response = $this->getJson("/api/auth/facebook/callback");

        $response->assertStatus(400);
        $response->assertJson(["message" => __("auth.facebook_error")]);
    }

    public function testLoginCallbackCreatesUserWithFirstNameFromFullName(): void
    {
        $this->mockSocialiteUser([
            "name" => "John Doe",
            "email" => "john@example.com",
            "id" => "fb123",
        ]);

        $response = $this->getJson("/api/auth/facebook/callback");

        $response->assertStatus(200);
        $this->assertDatabaseHas("users", [
            "email" => "john@example.com",
            "facebook_id" => "fb123",
            "first_name" => "John",
        ]);
    }

    public function testLoginCallbackCreatesUserWithFirstNameWhenSingleWordName(): void
    {
        $this->mockSocialiteUser([
            "name" => "Cher",
            "email" => "cher@example.com",
            "id" => "fb456",
        ]);

        $response = $this->getJson("/api/auth/facebook/callback");

        $response->assertStatus(200);
        $this->assertDatabaseHas("users", [
            "email" => "cher@example.com",
            "facebook_id" => "fb456",
            "first_name" => "Cher",
        ]);
    }

    public function testLoginCallbackFailsWhenNameIsMissing(): void
    {
        $this->mockSocialiteUser([
            "name" => null,
            "email" => "noname@example.com",
            "id" => "fb789",
        ]);

        $response = $this->getJson("/api/auth/facebook/callback");

        $response->assertStatus(422);
        $response->assertJson(["message" => __("auth.facebook_name_required")]);
        $this->assertDatabaseMissing("users", [
            "email" => "noname@example.com",
        ]);
    }

    public function testLoginCallbackFailsWhenNameIsEmptyString(): void
    {
        $this->mockSocialiteUser([
            "name" => "",
            "email" => "emptyname@example.com",
            "id" => "fb101",
        ]);

        $response = $this->getJson("/api/auth/facebook/callback");

        $response->assertStatus(422);
        $response->assertJson(["message" => __("auth.facebook_name_required")]);
        $this->assertDatabaseMissing("users", [
            "email" => "emptyname@example.com",
        ]);
    }

    public function testLoginCallbackExtractsOnlyFirstNameWhenMultipleFirstNames(): void
    {
        $this->mockSocialiteUser([
            "name" => "Mary Jane Smith",
            "email" => "mary@example.com",
            "id" => "fb202",
        ]);

        $response = $this->getJson("/api/auth/facebook/callback");

        $response->assertStatus(200);
        $this->assertDatabaseHas("users", [
            "email" => "mary@example.com",
            "facebook_id" => "fb202",
            "first_name" => "Mary",
        ]);
    }
}
