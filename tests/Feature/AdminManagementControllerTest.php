<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Testing\Fluent\AssertableJson;
use Interns2025b\Enums\Role;
use Interns2025b\Models\User;
use Tests\TestCase;

class AdminManagementControllerTest extends TestCase
{
    protected User $admin;
    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->superAdmin = User::factory()->superAdmin()->create();
    }

    public function testIndexReturnsOnlyAdmins(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->getJson("/api/admins");

        $response->assertOk();

        $response->assertJson(fn(AssertableJson $json): AssertableJson => $json->has(1)
            ->first(fn(AssertableJson $json): AssertableJson => $json
                ->where("id", $this->admin->id)
                ->where("email", $this->admin->email)
                ->where("facebook_linked", false)
                ->hasAll(["first_name", "last_name"])));
    }

    public function testIndexForbiddenForNonSuperAdmin(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson("/api/admins");

        $response->assertForbidden();
    }

    public function testShowReturnsAdmin(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->getJson("/api/admins/{$this->admin->id}");

        $response->assertOk();
        $response->assertJsonPath("id", $this->admin->id);
    }

    public function testShowRejectsNonAdminUser(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->superAdmin);

        $response = $this->getJson("/api/admins/{$user->id}");

        $response->assertForbidden();
    }

    public function testStoreCreatesAdminSuccessfully(): void
    {
        $this->actingAs($this->superAdmin);

        $payload = [
            "first_name" => "admin",
            "last_name" => "test",
            "email" => "newadmin@example.com",
            "password" => "password123",
            "password_confirmation" => "password123",
        ];

        $response = $this->postJson("/api/admins", $payload);

        $response->assertCreated();
        $this->assertDatabaseHas("users", ["email" => "newadmin@example.com"]);
        $this->assertEquals(
            [Role::Administrator->value],
            User::where("email", "newadmin@example.com")->first()->getRoleNames()->toArray(),
        );
    }

    public function testStoreRejectsDuplicateEmail(): void
    {
        User::factory()->create(["email" => "duplicate@example.com"]);

        $this->actingAs($this->superAdmin);

        $response = $this->postJson("/api/admins", [
            "first_name" => "admin",
            "last_name" => "dup",
            "email" => "duplicate@example.com",
            "password" => "password123",
            "password_confirmation" => "password123",
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(["email"]);
    }

    public function testUpdateModifiesAdminDetails(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->putJson("/api/admins/{$this->admin->id}", [
            "first_name" => "Updated",
            "email" => "updated@example.com",
        ]);

        $response->assertOk();

        $this->admin->refresh();
        $this->assertEquals("Updated", $this->admin->first_name);
        $this->assertEquals("updated@example.com", $this->admin->email);
    }

    public function testEmailChangeResetsEmailVerifiedAt(): void
    {
        $admin = User::factory()->admin()->create([
            "email" => "old@example.com",
            "email_verified_at" => now(),
        ]);

        $this->actingAs($this->superAdmin);

        $response = $this->putJson("/api/admins/{$admin->id}", [
            "email" => "new@example.com",
        ]);

        $response->assertOk();

        $admin->refresh();
        $this->assertEquals("new@example.com", $admin->email);
        $this->assertNull($admin->email_verified_at);
    }

    public function testEmailUnchangedKeepsEmailVerifiedAt(): void
    {
        $verifiedAt = now();
        $admin = User::factory()->admin()->create([
            "email" => "same@example.com",
            "email_verified_at" => $verifiedAt,
        ]);

        $this->actingAs($this->superAdmin);

        $response = $this->putJson("/api/admins/{$admin->id}", [
            "first_name" => "Updated",
        ]);

        $response->assertOk();

        $admin->refresh();
        $this->assertEquals("same@example.com", $admin->email);
        $this->assertEquals($verifiedAt->toDateTimeString(), $admin->email_verified_at->toDateTimeString());
    }

    public function testUpdateRejectsIfNotAdmin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->superAdmin);

        $response = $this->putJson("/api/admins/{$user->id}", [
            "first_name" => "shouldfail",
        ]);

        $response->assertForbidden();
    }

    public function testDestroyDeletesAdmin(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->deleteJson("/api/admins/{$this->admin->id}");

        $response->assertOk();
        $response->assertJson(["message" => "Admin deleted successfully."]);
        $this->assertDatabaseMissing("users", ["id" => $this->admin->id]);
    }

    public function testDestroyRejectsIfNotAdmin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->superAdmin);

        $response = $this->deleteJson("/api/admins/{$user->id}");

        $response->assertForbidden();
    }

    public function testOnlySuperAdminCanManageAdmins(): void
    {
        $target = User::factory()->admin()->create();

        $this->actingAs($this->admin);

        $response = $this->deleteJson("/api/admins/{$target->id}");

        $response->assertForbidden();
    }

    public function testGuestCannotAccessAdminRoutes(): void
    {
        $endpoints = [
            "get" => "/api/admins",
            "post" => "/api/admins",
            "get_show" => "/api/admins/{$this->admin->id}",
            "put" => "/api/admins/{$this->admin->id}",
            "delete" => "/api/admins/{$this->admin->id}",
        ];

        foreach ($endpoints as $method => $uri) {
            $response = match ($method) {
                "get", "get_show" => $this->getJson($uri),
                "post" => $this->postJson($uri, []),
                "put" => $this->putJson($uri, []),
                "delete" => $this->deleteJson($uri),
            };

            $response->assertUnauthorized();
        }
    }

    public function testUpdateWithSameEmailKeepsVerification(): void
    {
        $verifiedAt = now();
        $this->admin->update(["email_verified_at" => $verifiedAt]);

        $this->actingAs($this->superAdmin);

        $response = $this->putJson("/api/admins/{$this->admin->id}", [
            "email" => $this->admin->email,
        ]);

        $response->assertOk();
        $this->assertEquals($verifiedAt->toDateTimeString(), $this->admin->fresh()->email_verified_at->toDateTimeString());
    }

    public function testSuperAdminCannotBeDemotedToAdmin(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->putJson("/api/admins/{$this->superAdmin->id}", [
            "first_name" => "Attempted Demotion",
        ]);

        $response->assertForbidden();
    }

    public function testAdminCannotDeleteSelf(): void
    {
        $this->actingAs($this->admin);

        $response = $this->deleteJson("/api/admins/{$this->admin->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas("users", ["id" => $this->admin->id]);
    }
}
