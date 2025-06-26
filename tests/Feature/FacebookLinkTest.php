<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Interns2025b\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\MocksFacebookUser;

class FacebookLinkTest extends TestCase
{
    use RefreshDatabase;
    use MocksFacebookUser;

    public function testCallbackLinksFacebookAccountSuccessfully(): void
    {
        $this->mockSocialiteUser(["id" => "new_fb_id"]);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/link/facebook/callback");

        $response->assertStatus(200);
        $response->assertJson(["message" => __("auth.facebook_link_success")]);

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
        $response->assertJson(["message" => __("auth.facebook_account_already_linked")]);
    }

    public function testCallbackFailsWhenUserExistsWithSameEmailButNoFacebookId(): void
    {
        User::factory()->create([
            "email" => "test@example.com",
            "facebook_id" => null,
        ]);

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->mockSocialiteUser([
            "email" => "test@example.com",
            "id" => "some_facebook_id",
        ]);

        $response = $this->getJson("/api/link/facebook/callback");

        $response->assertStatus(403);
        $response->assertJson(["message" => __("auth.email_already_registered")]);
    }

    public function testCallbackFailsWhenFacebookIdIsMissing(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->mockSocialiteUser(["id" => null]);

        $response = $this->getJson("/api/link/facebook/callback");

        $response->assertStatus(422);
        $response->assertJson(["message" => __("auth.facebook_id_required")]);
    }

    public function testCallbackFailsWhenFacebookEmailIsMissing(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->mockSocialiteUser(["email" => null]);

        $response = $this->getJson("/api/link/facebook/callback");

        $response->assertStatus(422);
        $response->assertJson(["message" => __("auth.email_required_from_facebook")]);
    }
}
