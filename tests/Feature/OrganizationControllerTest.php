<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;
use Tests\TestCase;

class OrganizationControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->create();
    }

    public function testAdminCanListOrganizations(): void
    {
        Organization::factory()->count(2)->create();

        $this->actingAs($this->admin);

        $response = $this->getJson("/api/admin/organizations");

        $response->assertOk()
            ->assertJsonCount(2);
    }

    public function testNonAdminCannotListOrganizations(): void
    {
        $this->actingAs($this->user);

        $response = $this->getJson("/api/admin/organizations");

        $response->assertForbidden();
    }

    public function testAdminCanViewOrganizationDetails(): void
    {
        $organization = Organization::factory()->create();

        $this->actingAs($this->admin);

        $response = $this->getJson("/api/organizations/{$organization->id}");

        $response->assertOk()
            ->assertJsonPath("data.id", $organization->id);
    }

    public function testShowReturns404ForNonexistentOrganization(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson("/api/organizations/999");

        $response->assertNotFound();
    }

    public function testAdminCanCreateOrganization(): void
    {
        $this->actingAs($this->admin);

        $payload = ["name" => "New Organization"];

        $response = $this->postJson("/api/admin/organizations", $payload);

        $response->assertCreated()
            ->assertJsonPath("data.name", "New Organization");

        $this->assertDatabaseHas("organizations", ["name" => "New Organization"]);
    }

    public function testCreateFailsWithInvalidData(): void
    {
        $this->actingAs($this->admin);

        $response = $this->postJson("/api/admin/organizations", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(["name"]);
    }

    public function testNonAdminCannotCreateOrganization(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/admin/organizations", ["name" => "Should Fail"]);

        $response->assertForbidden();
    }

    public function testAdminCanUpdateOrganization(): void
    {
        $organization = Organization::factory()->create(["name" => "Old Name"]);

        $this->actingAs($this->admin);

        $response = $this->putJson("/api/admin/organizations/{$organization->id}", ["name" => "Updated Name"]);

        $response->assertOk()
            ->assertJsonPath("data.name", "Updated Name");

        $this->assertDatabaseHas("organizations", ["id" => $organization->id, "name" => "Updated Name"]);
    }

    public function testUpdateFailsWithInvalidData(): void
    {
        $organization = Organization::factory()->create();

        $this->actingAs($this->admin);

        $response = $this->putJson("/api/admin/organizations/{$organization->id}", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(["name"]);
    }

    public function testUpdateFailsForNonexistentOrganization(): void
    {
        $this->actingAs($this->admin);

        $response = $this->putJson("/api/admin/organizations/999", ["name" => "Non-existent"]);

        $response->assertNotFound();
    }

    public function testAdminCanDeleteOrganization(): void
    {
        $organization = Organization::factory()->create();

        $this->actingAs($this->admin);

        $response = $this->deleteJson("/api/admin/organizations/{$organization->id}");

        $response->assertOk()
            ->assertJson(["message" => "Organization deleted successfully."]);

        $this->assertDatabaseMissing("organizations", ["id" => $organization->id]);
    }

    public function testDeleteFailsForNonexistentOrganization(): void
    {
        $this->actingAs($this->admin);

        $response = $this->deleteJson("/api/admin/organizations/999");

        $response->assertNotFound();
    }

    public function testNonAdminCannotUpdateOrDelete(): void
    {
        $organization = Organization::factory()->create();

        $this->actingAs($this->user);

        $this->putJson("/api/admin/organizations/{$organization->id}", ["name" => "Blocked"])
            ->assertForbidden();

        $this->deleteJson("/api/admin/organizations/{$organization->id}")
            ->assertForbidden();
    }
}
