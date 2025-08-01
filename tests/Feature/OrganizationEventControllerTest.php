<?php

declare(strict_types=1);

namespace Tests\Feature;

use Interns2025b\Models\Event;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrganizationEventControllerTest extends TestCase
{
    private User $user;
    private Organization $org;
    private Organization $org1;
    private Organization $org2;
    private array $validPayload;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        [$this->org, $this->org1, $this->org2] = collect(range(1, 3))
            ->map(fn(int $i): Organization => $this->setUpOrganizationWithUser())
            ->all();

        $this->validPayload = [
            "title" => "Test Event",
            "start" => now()->addDay()->toDateTimeString(),
            "end" => now()->addDays(2)->toDateTimeString(),
            "location" => "Test Location",
            "is_paid" => true,
            "status" => "published",
        ];

        Sanctum::actingAs($this->user);
    }

    public function testUserCanListOrganizationEvents(): void
    {
        Event::factory()->count(3)->for($this->org, "owner")->create();

        $response = $this->getJson("/api/organizations/{$this->org->id}/events");

        $response->assertOk()->assertJsonCount(3, "data");
    }

    public function testUserCanCreateEventInOrganizationTheyBelongTo(): void
    {
        $response = $this->postJson("/api/organizations/{$this->org->id}/events", $this->validPayload);

        $response->assertCreated()->assertJsonPath("data.title", "Test Event");
    }

    public function testUserCannotCreateEventInUnrelatedOrganization(): void
    {
        $unrelatedOrg = Organization::factory()->create();

        $response = $this->postJson("/api/organizations/{$unrelatedOrg->id}/events", $this->validPayload);

        $response->assertForbidden()
            ->assertJson(["message" => "This action is unauthorized."]);
    }

    public function testUserCanUpdateEventInOrganization(): void
    {
        $event = $this->org->events()->create(
            Event::factory()->make([
                "title" => "Original Event",
                "start" => now()->addDay()->toDateTimeString(),
                "end" => now()->addDays(2)->toDateTimeString(),
                "location" => "Old Location",
                "is_paid" => true,
                "status" => "draft",
            ])->toArray(),
        );

        $updatePayload = [
            "title" => "Updated Event",
            "start" => now()->addDay()->toDateTimeString(),
            "end" => now()->addDays(2)->toDateTimeString(),
            "location" => "Updated Location",
            "is_paid" => false,
            "status" => "ongoing",
        ];

        $response = $this->putJson("/api/organizations/{$this->org->id}/events/{$event->id}", $updatePayload);

        $response->assertOk()->assertJsonPath("data.title", "Updated Event");
    }

    public function testUserCanDeleteEventFromOrganization(): void
    {
        $event = $this->org->events()->create(
            Event::factory()->make([
                "title" => "Event to Delete",
                "start" => now()->addDay()->toDateTimeString(),
                "end" => now()->addDays(2)->toDateTimeString(),
                "location" => "Somewhere",
                "is_paid" => true,
                "status" => "published",
            ])->toArray(),
        );

        $response = $this->deleteJson("/api/organizations/{$this->org->id}/events/{$event->id}");

        $response->assertOk()->assertJson(["message" => "Event deleted from organization."]);
        $this->assertDatabaseMissing("events", ["id" => $event->id]);
    }

    public function testUserWithMultipleOrganizationsCanOnlyCreateEventInCorrectOne(): void
    {
        $response = $this->postJson("/api/organizations/{$this->org1->id}/events", $this->validPayload);

        $response->assertCreated()->assertJsonPath("data.title", "Test Event");
    }

    public function testUserCannotUpdateEventThatBelongsToDifferentOrganization(): void
    {
        $event = Event::factory()->for($this->org2, "owner")->create();

        $updatePayload = [
            "title" => "Invalid Update",
            "start" => now()->addDay()->toDateTimeString(),
            "end" => now()->addDays(2)->toDateTimeString(),
        ];

        $response = $this->putJson("/api/organizations/{$this->org1->id}/events/{$event->id}", $updatePayload);

        $response->assertNotFound();
    }

    public function testUserCannotDeleteEventThatBelongsToDifferentOrganization(): void
    {
        $event = Event::factory()->for($this->org2, "owner")->create();

        $response = $this->deleteJson("/api/organizations/{$this->org1->id}/events/{$event->id}");

        $response->assertNotFound();
    }

    public function testCreatingEventWithMissingRequiredFieldsFails(): void
    {
        $response = $this->postJson("/api/organizations/{$this->org->id}/events", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(["title", "start", "end", "location", "is_paid", "status"]);
    }

    public function testCreatingEventWithEndDateBeforeStartFails(): void
    {
        $payload = [
            "title" => "Invalid Time Event",
            "start" => now()->addDays(2)->toDateTimeString(),
            "end" => now()->addDay()->toDateTimeString(),
            "location" => "Test Location",
            "is_paid" => true,
            "status" => "published",
        ];

        $response = $this->postJson("/api/organizations/{$this->org->id}/events", $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(["end"]);
    }

    public function testUserWithoutOrganizationCannotCreateEventThroughOrganization(): void
    {
        $this->user->organizations()->detach();

        $org = Organization::factory()->create();

        $payload = [
            "title" => "Unauthorized Event",
            "start" => now()->addDay()->toDateTimeString(),
            "end" => now()->addDays(2)->toDateTimeString(),
            "location" => "Nowhere",
            "is_paid" => false,
            "status" => "draft",
        ];

        Sanctum::actingAs($this->user);

        $response = $this->postJson("/api/organizations/{$org->id}/events", $payload);

        $response->assertForbidden()->assertJson(["message" => "This action is unauthorized."]);
    }

    private function setUpOrganizationWithUser(): Organization
    {
        $org = Organization::factory()->create();
        $org->users()->attach($this->user);

        return $org;
    }
}
