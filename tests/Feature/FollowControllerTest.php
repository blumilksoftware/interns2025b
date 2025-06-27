<?php

declare(strict_types=1);

namespace Tests\Feature;

use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;
use Tests\TestCase;

class FollowControllerTest extends TestCase
{
    public function testFollowAndUnfollowViaApi(): void
    {
        $user = User::factory()->create();
        $targetUser = User::factory()->create();

        $this->actingAs($user, "sanctum");

        $response = $this->postJson("/api/follow/user/$targetUser->id");
        $response->assertStatus(Status::HTTP_OK)
            ->assertJson(["message" => __("follow.followed")]);

        $response = $this->getJson("/api/followings");
        $response->assertStatus(Status::HTTP_OK);
        $this->assertTrue(collect($response->json("users"))->pluck("id")->contains($targetUser->id));

        $response = $this->postJson("/api/follow/user/$targetUser->id");
        $response->assertStatus(Status::HTTP_OK)
            ->assertJson(["message" => __("follow.unfollowed")]);

        $response = $this->getJson("/api/followings");
        $response->assertStatus(Status::HTTP_OK);
        $this->assertFalse(collect($response->json("users"))->pluck("id")->contains($targetUser->id));
    }

    public function testCannotFollowNonexistentModel(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, "sanctum");

        $response = $this->postJson("/api/follow/user/999999");
        $response->assertStatus(Status::HTTP_BAD_REQUEST)
            ->assertJson(["message" => __("follow.error")]);
    }

    public function testCannotFollowSelf(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, "sanctum");

        $response = $this->postJson("/api/follow/user/$user->id");
        $response->assertStatus(Status::HTTP_BAD_REQUEST)
            ->assertJson(["message" => __("follow.cannot_follow_self")]);
    }

    public function testFollowingsAndFollowersApi(): void
    {
        $user = User::factory()->create();
        $targetUser = User::factory()->create();

        $this->actingAs($user, "sanctum");

        $this->postJson("/api/follow/user/$targetUser->id")->assertStatus(200);

        $response = $this->getJson("/api/followings");
        $response->assertStatus(Status::HTTP_OK)
            ->assertJsonFragment(["id" => $targetUser->id]);

        $this->actingAs($targetUser, "sanctum");
        $response = $this->getJson("/api/followers");
        $response->assertStatus(Status::HTTP_OK)
            ->assertJsonFragment(["id" => $user->id]);
    }
}
