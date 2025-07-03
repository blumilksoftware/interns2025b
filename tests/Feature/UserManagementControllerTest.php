<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Testing\Fluent\AssertableJson;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;
use Tests\TestCase;

class UserManagementControllerTest extends TestCase
{
    protected User $admin;
    protected User $userWithRole;
    protected User $anotherAdmin;
    protected Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a common admin user
        $this->admin = User::factory()->admin()->create([
            "first_name" => "admin",
        ]);

        // Create a regular user with an organization
        $this->userWithRole = User::factory()->create([
            "first_name" => "user",
        ]);
        $this->organization = Organization::factory()->create();
        $this->userWithRole->organizations()->attach($this->organization);

        // Create another admin user for tests needing multiple admins
        $this->anotherAdmin = User::factory()->admin()->create();
    }

    public function testIndexReturnsOnlyUsersWithUserRoleAndOrganizations(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson("/api/admin/users");

        $response->assertOk();

        $response->assertJson(
            fn(AssertableJson $json) => $json->has(1)
                ->first(
                    fn($json) => $json->where("id", $this->userWithRole->id)
                        ->where("first_name", "user")
                        ->where("last_name", $this->userWithRole->last_name)
                        ->where("email", $this->userWithRole->email)
                        ->where("facebook_linked", false)
                        ->where("email_verified_at", $this->userWithRole->email_verified_at ? $this->userWithRole->email_verified_at->toJSON() : null)
                        ->where("created_at", $this->userWithRole->created_at->toJSON())
                        ->where("updated_at", $this->userWithRole->updated_at->toJSON())
                        ->has("organizations", 1),
                ),
        );
    }

    public function testUsersIndexDoesNotIncludeAdmins(): void
    {
        $superAdmin = User::factory()->superAdmin()->create([
            "first_name" => "admin",
        ]);

        $this->actingAs($this->admin);

        $response = $this->getJson("/api/admin/users");

        $response->assertOk();

        $ids = collect($response->json())->pluck("id")->toArray();

        $this->assertNotContains($this->admin->id, $ids);
        $this->assertNotContains($superAdmin->id, $ids);
    }

    public function testNonAdminCannotAccessUsersIndex(): void
    {
        $this->actingAs($this->userWithRole);

        $response = $this->getJson("/api/admin/users");

        $response->assertForbidden();
    }

    public function testShowReturnsUserWithUserOrganizations(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson("/api/admin/users/{$this->userWithRole->id}");

        $response->assertOk();
        $response->assertJsonPath("id", $this->userWithRole->id);
        $response->assertJsonPath("organizations.0", $this->organization->id);
    }

    public function testShowRejectsAdminUser(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson("/api/admin/users/{$this->anotherAdmin->id}");

        $response->assertForbidden();
    }

    public function testStoreCreatesUserSuccessfully(): void
    {
        $this->actingAs($this->admin);

        $payload = [
            "first_name" => "user",
            "last_name" => "test",
            "email" => "newuser@example.com",
            "password" => "password123",
            "password_confirmation" => "password123",
            "organization_ids" => [$this->organization->id],
        ];

        $response = $this->postJson("/api/admin/users", $payload);

        $response->assertCreated();
        $this->assertDatabaseHas("users", ["email" => "newuser@example.com"]);
        $this->assertEquals(["user"], User::where("email", "newuser@example.com")->first()->getRoleNames()->toArray());
    }

    public function testStoreRejectsDuplicateEmail(): void
    {
        User::factory()->create(["email" => "duplicate@example.com"]);

        $this->actingAs($this->admin);

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
        $newOrg = Organization::factory()->create();

        $this->actingAs($this->admin);

        $response = $this->putJson("/api/admin/users/{$this->userWithRole->id}", [
            "first_name" => "updated",
            "email" => "updated@example.com",
            "organization_ids" => [$newOrg->id],
        ]);

        $response->assertOk();

        $this->userWithRole->refresh();

        $this->assertEquals("updated", $this->userWithRole->first_name);
        $this->assertEquals("updated@example.com", $this->userWithRole->email);
        $this->assertTrue($this->userWithRole->organizations->pluck("id")->contains($newOrg->id));
    }

    public function testEmailChangeResetsEmailVerifiedAt(): void
    {
        $user = User::factory()->create([
            "email" => "old@example.com",
            "email_verified_at" => now(),
        ]);
        $user->assignRole("user");

        $this->actingAs($this->admin);

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
        $verifiedAt = now();
        $user = User::factory()->create([
            "email" => "same@example.com",
            "email_verified_at" => $verifiedAt,
        ]);
        $user->assignRole("user");

        $this->actingAs($this->admin);

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
        $this->actingAs($this->admin);

        $response = $this->putJson("/api/admin/users/{$this->anotherAdmin->id}", [
            "first_name" => "shouldfail",
        ]);

        $response->assertForbidden();
    }

    public function testDestroyDeletesUser(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin);

        $response = $this->deleteJson("/api/admin/users/{$user->id}");

        $response->assertOk();
        $response->assertJson(["message" => "User deleted successfully."]);
        $this->assertDatabaseMissing("users", ["id" => $user->id]);
    }

    public function testDestroyRejectsIfUserIsAdmin(): void
    {
        $this->actingAs($this->admin);

        $response = $this->deleteJson("/api/admin/users/{$this->anotherAdmin->id}");

        $response->assertForbidden();
    }
}
