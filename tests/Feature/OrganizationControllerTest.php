<?php

declare(strict_types=1);

namespace Tests\Feature;

use Interns2025b\Models\Organization;
use Interns2025b\Models\User;
use Tests\TestCase;

class OrganizationControllerTest extends TestCase
{
    private User $admin;
    private User $user;
    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create();
    }

    public function testAdminCanListOrganizations(): void
    {
        Organization::factory()->count(2)->create();

        $this->actingAs($this->admin);

        $response = $this->getJson("/api/admin/organizations");

        $response->assertOk()
            ->assertJsonCount(3, "data");
    }

    public function testNonAdminCannotListOrganizations(): void
    {
        $this->actingAs($this->user);

        $response = $this->getJson("/api/admin/organizations");

        $response->assertForbidden();
    }

    public function testAdminCanViewOrganizationDetails(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson("/api/organizations/{$this->organization->id}");

        $response->assertOk()
            ->assertJsonPath("data.id", $this->organization->id);
    }

    public function testShowReturns404ForNonexistentOrganization(): void
    {
        $this->actingAs($this->admin);

        $response = $this->getJson("/api/organizations/999999");

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
        $this->actingAs($this->admin);

        $response = $this->putJson("/api/admin/organizations/{$this->organization->id}", ["name" => "Updated Name"]);

        $response->assertOk()
            ->assertJsonPath("data.name", "Updated Name");

        $this->assertDatabaseHas("organizations", ["id" => $this->organization->id, "name" => "Updated Name"]);
    }

    public function testUpdateFailsWithInvalidData(): void
    {
        $this->actingAs($this->admin);

        $response = $this->putJson("/api/admin/organizations/{$this->organization->id}", []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(["name"]);
    }

    public function testUpdateFailsForNonexistentOrganization(): void
    {
        $this->actingAs($this->admin);

        $response = $this->putJson("/api/admin/organizations/999999", ["name" => "Non-existent"]);

        $response->assertNotFound();
    }

    public function testAdminCanDeleteOrganization(): void
    {
        $org = Organization::factory()->create();

        $this->actingAs($this->admin);

        $response = $this->deleteJson("/api/admin/organizations/{$org->id}");

        $response->assertOk()
            ->assertJson(["message" => "Organization deleted successfully."]);

        $this->assertDatabaseMissing("organizations", ["id" => $org->id]);
    }

    public function testDeleteFailsForNonexistentOrganization(): void
    {
        $this->actingAs($this->admin);

        $response = $this->deleteJson("/api/admin/organizations/999999");

        $response->assertNotFound();
    }

    public function testNonAdminCannotUpdateOrDelete(): void
    {
        $this->actingAs($this->user);

        $this->putJson("/api/admin/organizations/{$this->organization->id}", ["name" => "Blocked"])
            ->assertForbidden();

        $this->deleteJson("/api/admin/organizations/{$this->organization->id}")
            ->assertForbidden();
    }

    public function testOrganizationOwnerCannotCreateOrganization(): void
    {
        $owner = User::factory()->create();

        $this->actingAs($owner);

        $response = $this->postJson("/api/admin/organizations", ["name" => "Not Allowed"]);

        $response->assertForbidden();
    }

    public function testOrganizationUpdateDoesNotChangeImmutableFields(): void
    {
        $this->actingAs($this->admin);

        $payload = [
            "name" => "New Name",
            "id" => 999,
            "owner_id" => User::factory()->create()->id,
            "created_at" => now()->subYears(2)->toDateTimeString(),
        ];

        $this->putJson("/api/admin/organizations/{$this->organization->id}", $payload)
            ->assertOk()
            ->assertJsonPath("data.name", "New Name");

        $this->assertDatabaseMissing("organizations", ["id" => 999]);
        $this->assertDatabaseHas("organizations", ["id" => $this->organization->id, "name" => "New Name"]);
    }

    public function testDeletingOrganizationRemovesItFromIndex(): void
    {
        $org = Organization::factory()->create(["name" => "To Be Deleted"]);
        $this->actingAs($this->admin);

        $this->deleteJson("/api/admin/organizations/{$org->id}")
            ->assertOk();

        $this->getJson("/api/admin/organizations")
            ->assertOk()
            ->assertJsonMissing(["name" => "To Be Deleted"]);
    }

    public function testOwnerCannotChangeOtherOwnersOrganization(): void
    {
        $ownerA = User::factory()->create();
        $ownerB = User::factory()->create();
        $organization = Organization::factory()->for($ownerB, "owner")->create(["name" => "Owned By B"]);

        $this->actingAs($ownerA);

        $this->putJson("/api/admin/organizations/{$organization->id}", ["name" => "Should Fail"])
            ->assertForbidden();

        $this->deleteJson("/api/admin/organizations/{$organization->id}")
            ->assertForbidden();
    }
}
