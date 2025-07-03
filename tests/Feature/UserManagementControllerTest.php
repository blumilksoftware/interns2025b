<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Testing\Fluent\AssertableJson;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;
use Tests\TestCase;

class UserManagementControllerTest extends TestCase
{
    public function testIndexReturnsOnlyUsersWithUserRoleAndOrganizations(): void
    {
        $userWithRole = User::factory()->create([
            "first_name" => "user",
        ]);

        $organization = Organization::factory()->create();
        $userWithRole->organizations()->attach($organization);

        $adminUser = User::factory()->admin()->create([
            "first_name" => "admin",
        ]);

        $this->actingAs($adminUser);

        $response = $this->getJson("/api/admin/users");

        $response->assertOk();

        $response->assertJson(
            fn(AssertableJson $json) => $json->has(1)
                ->first(
                    fn($json) => $json->where("id", $userWithRole->id)
                        ->where("first_name", "user")
                        ->where("last_name", $userWithRole->last_name)
                        ->where("email", $userWithRole->email)
                        ->where("facebook_linked", false)
                        ->where("email_verified_at", $userWithRole->email_verified_at ? $userWithRole->email_verified_at->toJSON() : null)
                        ->where("created_at", $userWithRole->created_at->toJSON())
                        ->where("updated_at", $userWithRole->updated_at->toJSON())
                        ->has("organizations", 1),
                ),
        );
    }

    public function testUsersIndexDoesNotIncludeAdmins(): void
    {
        $user = User::factory()->create([
            "first_name" => "user",
        ]);

        $admin = User::factory()->admin()->create([
            "first_name" => "admin",
        ]);

        $superAdmin = User::factory()->superAdmin()->create([
            "first_name" => "admin",
        ]);

        $this->actingAs($admin);

        $response = $this->getJson("/api/admin/users");

        $response->assertOk();

        $ids = collect($response->json())->pluck("id")->toArray();

        $this->assertNotContains($admin->id, $ids);
        $this->assertNotContains($superAdmin->id, $ids);
    }

    public function testNonAdminCannotAccessUsersIndex(): void
    {
        $regularUser = User::factory()->create([
            "first_name" => "user",
        ]);

        $this->actingAs($regularUser);

        $response = $this->getJson("/api/admin/users");

        $response->assertForbidden();
    }

    public function testShowReturnsUserWithUserOrganizations(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $organization = Organization::factory()->create();
        $user->organizations()->attach($organization);

        $this->actingAs($admin);

        $response = $this->getJson("/api/admin/users/{$user->id}");

        $response->assertOk();
        $response->assertJsonPath("id", $user->id);
        $response->assertJsonPath("organizations.0", $organization->id);
    }

    public function testShowRejectsAdminUser(): void
    {
        $admin = User::factory()->admin()->create();

        $anotherAdmin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $response = $this->getJson("/api/admin/users/{$anotherAdmin->id}");

        $response->assertForbidden();
    }

    public function testStoreCreatesUserSuccessfully(): void
    {
        $admin = User::factory()->admin()->create();
        $organization = Organization::factory()->create();

        $this->actingAs($admin);

        $payload = [
            "first_name" => "user",
            "last_name" => "test",
            "email" => "newuser@example.com",
            "password" => "password123",
            "password_confirmation" => "password123",
            "organization_ids" => [$organization->id],
        ];

        $response = $this->postJson("/api/admin/users", $payload);

        $response->assertCreated();
        $this->assertDatabaseHas("users", ["email" => "newuser@example.com"]);
        $this->assertEquals(["user"], User::where("email", "newuser@example.com")->first()->getRoleNames()->toArray());
    }

    public function testStoreRejectsDuplicateEmail(): void
    {
        $admin = User::factory()->admin()->create();
        $existingUser = User::factory()->create(["email" => "duplicate@example.com"]);

        $this->actingAs($admin);

        $response = $this->postJson("/api/admin/users", [
            "first_name" => "user",
            "last_name" => "dup",
            "email" => "duplicate@example.com",
            "password" => "password123",
            "password_confirmation" => "password123",
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(["email"]);
    }

    public function testUpdateModifiesUserDetails(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(["email" => "original@example.com"]);
        $newOrg = Organization::factory()->create();

        $this->actingAs($admin);

        $response = $this->putJson("/api/admin/users/{$user->id}", [
            "first_name" => "updated",
            "email" => "updated@example.com",
            "organization_ids" => [$newOrg->id],
        ]);

        $response->assertOk();

        $user->refresh();

        $this->assertEquals("updated", $user->first_name);
        $this->assertEquals("updated@example.com", $user->email);
        $this->assertTrue($user->organizations->pluck("id")->contains($newOrg->id));
    }

    public function testEmailChangeResetsEmailVerifiedAt(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            "email" => "old@example.com",
            "email_verified_at" => now(),
        ]);
        $user->assignRole("user");

        $this->actingAs($admin);

        $response = $this->putJson("/api/admin/users/{$user->id}", [
            "email" => "new@example.com",
        ]);

        $response->assertOk();

        $user->refresh();
        $this->assertEquals("new@example.com", $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function testEmailUnchangedKeepsEmailVerifiedAt(): void
    {
        $admin = User::factory()->admin()->create();
        $verifiedAt = now();
        $user = User::factory()->create([
            "email" => "same@example.com",
            "email_verified_at" => $verifiedAt,
        ]);
        $user->assignRole("user");

        $this->actingAs($admin);

        $response = $this->putJson("/api/admin/users/{$user->id}", [
            "first_name" => "Updated",
        ]);

        $response->assertOk();

        $user->refresh();
        $this->assertEquals("same@example.com", $user->email);
        $this->assertEquals($verifiedAt->toDateTimeString(), $user->email_verified_at->toDateTimeString());
    }

    public function testUpdateRejectsIfNotUserRole(): void
    {
        $admin = User::factory()->admin()->create();

        $adminTarget = User::factory()->admin()->create();

        $this->actingAs($admin);

        $response = $this->putJson("/api/admin/users/{$adminTarget->id}", [
            "first_name" => "shouldfail",
        ]);

        $response->assertForbidden();
    }

    public function testDestroyDeletesUser(): void
    {
        $admin = User::factory()->admin()->create();

        $user = User::factory()->create();

        $this->actingAs($admin);

        $response = $this->deleteJson("/api/admin/users/{$user->id}");

        $response->assertOk();
        $response->assertJson(["message" => "User deleted successfully."]);
        $this->assertDatabaseMissing("users", ["id" => $user->id]);
    }

    public function testDestroyRejectsIfUserIsAdmin(): void
    {
        $admin = User::factory()->admin()->create();
        $targetAdmin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $response = $this->deleteJson("/api/admin/users/{$targetAdmin->id}");

        $response->assertForbidden();
    }
}
