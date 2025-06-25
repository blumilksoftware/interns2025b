<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;
use Tests\TestCase;

class OrganizationManagementControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexReturnsAllOrganizations(): void
    {
        $admin = User::factory()->admin()->create();
        $org1 = Organization::factory()->create(["name" => "Org A"]);
        $org2 = Organization::factory()->create(["name" => "Org B"]);

        $this->actingAs($admin);

        $response = $this->getJson("/api/admin/organizations");

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJsonFragment(["name" => "Org A"]);
        $response->assertJsonFragment(["name" => "Org B"]);
    }

    public function testShowReturnsSpecificOrganization(): void
    {
        $admin = User::factory()->admin()->create();
        $organization = Organization::factory()->create();

        $this->actingAs($admin);

        $response = $this->getJson("/api/organizations/{$organization->id}");

        $response->assertOk();
        $response->assertJsonPath("id", $organization->id);
        $response->assertJsonPath("name", $organization->name);
    }

    public function testNonAdminCannotAccessIndex(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->getJson("/api/admin/organizations");

        $response->assertForbidden();
    }

    public function testStoreCreatesOrganizationSuccessfully(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $payload = [
            "name" => "New Org",
        ];

        $response = $this->postJson("/api/admin/organizations", $payload);

        $response->assertCreated();
        $this->assertDatabaseHas("organizations", ["name" => "New Org"]);
    }

    public function testStoreFailsWithInvalidData(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $response = $this->postJson("/api/admin/organizations", []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(["name"]);
    }

    public function testUpdateModifiesOrganizationDetails(): void
    {
        $admin = User::factory()->admin()->create();
        $organization = Organization::factory()->create(["name" => "Old Name"]);

        $this->actingAs($admin);

        $response = $this->putJson("/api/admin/organizations/{$organization->id}", [
            "name" => "Updated Name",
        ]);

        $response->assertOk();
        $organization->refresh();
        $this->assertEquals("Updated Name", $organization->name);
    }

    public function testDestroyDeletesOrganization(): void
    {
        $admin = User::factory()->admin()->create();
        $organization = Organization::factory()->create();

        $this->actingAs($admin);

        $response = $this->deleteJson("/api/admin/organizations/{$organization->id}");

        $response->assertOk();
        $response->assertJson(["message" => "Organization deleted successfully."]);
        $this->assertDatabaseMissing("organizations", ["id" => $organization->id]);
    }
}
