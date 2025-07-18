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
    public function testUserCanListOrganizationEvents(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $org = Organization::factory()->create();
        $org->users()->attach($user);

        Event::factory()->count(3)->for($org, "owner")->create();

        $response = $this->getJson("/api/organizations/{$org->id}/events");

        $response->assertOk()->assertJsonCount(3, "data");
    }

    public function testUserCanCreateEventInOrganizationTheyBelongTo(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create();
        $org->users()->attach($user);

        Sanctum::actingAs($user);

        $payload = [
            "title" => "Test Event",
            "start_time" => now()->addDay()->toDateTimeString(),
            "end_time" => now()->addDays(2)->toDateTimeString(),
            "location" => "Test Location",
            "is_paid" => true,
            "status" => "published",
        ];

        $response = $this->postJson("/api/organizations/{$org->id}/events", $payload);

        $response->assertCreated()
            ->assertJsonPath("data.title", "Test Event");
    }

    public function testUserCannotCreateEventInUnrelatedOrganization(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create();

        Sanctum::actingAs($user);

        $payload = [
            "title" => "Unauthorized Event",
            "start_time" => now()->addDay()->toDateTimeString(),
            "end_time" => now()->addDays(2)->toDateTimeString(),
            "location" => "Test Location",
            "is_paid" => true,
            "status" => "published",
        ];

        $response = $this->postJson("/api/organizations/{$org->id}/events", $payload);

        $response->assertForbidden()
            ->assertJson(["message" => "This action is unauthorized."]);
    }

    public function testUserCanUpdateEventInOrganization(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create();
        $org->users()->attach($user);

        $event = Event::factory()->for($org, "owner")->create();

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/organizations/{$org->id}/events/{$event->id}", [
            "title" => "Updated Event",
            "start_time" => now()->addDay()->toDateTimeString(),
            "end_time" => now()->addDays(2)->toDateTimeString(),
            "location" => "Updated Location",
            "is_paid" => false,
            "status" => "ongoing",
        ]);

        $response->assertOk()
            ->assertJsonPath("data.title", "Updated Event");
    }

    public function testUserCanDeleteEventFromOrganization(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create();
        $org->users()->attach($user);

        $event = Event::factory()->for($org, "owner")->create();

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/organizations/{$org->id}/events/{$event->id}");

        $response->assertOk()
            ->assertJson(["message" => "Event deleted from organization."]);
        $this->assertDatabaseMissing("events", ["id" => $event->id]);
    }

    public function testUserWithMultipleOrganizationsCanOnlyCreateEventInCorrectOne(): void
    {
        $user = User::factory()->create();
        $org1 = Organization::factory()->create();
        $org2 = Organization::factory()->create();
        $org1->users()->attach($user);
        $org2->users()->attach($user);

        Sanctum::actingAs($user);

        $payload = [
            "title" => "Org1 Event",
            "start_time" => now()->addDay()->toDateTimeString(),
            "end_time" => now()->addDays(2)->toDateTimeString(),
            "location" => "Test Location",
            "is_paid" => true,
            "status" => "published",
        ];

        $response = $this->postJson("/api/organizations/{$org1->id}/events", $payload);

        $response->assertCreated()
            ->assertJsonPath("data.title", "Org1 Event");
    }

    public function testUserCannotUpdateEventThatBelongsToDifferentOrganization(): void
    {
        $user = User::factory()->create();
        $org1 = Organization::factory()->create();
        $org2 = Organization::factory()->create();
        $org1->users()->attach($user);
        $org2->users()->attach($user);

        $event = Event::factory()->for($org2, "owner")->create();

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/organizations/{$org1->id}/events/{$event->id}", [
            "title" => "Invalid Update",
            "start" => now()->addDay()->toDateTimeString(),
            "end" => now()->addDays(2)->toDateTimeString(),
        ]);

        $response->assertNotFound();
    }

    public function testUserCannotDeleteEventThatBelongsToDifferentOrganization(): void
    {
        $user = User::factory()->create();
        $org1 = Organization::factory()->create();
        $org2 = Organization::factory()->create();
        $org1->users()->attach($user);
        $org2->users()->attach($user);

        $event = Event::factory()->for($org2, "owner")->create();

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/organizations/{$org1->id}/events/{$event->id}");

        $response->assertNotFound();
    }

    public function testCreatingEventWithMissingRequiredFieldsFails(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create();
        $org->users()->attach($user);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/organizations/{$org->id}/events", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(["title", "start_time", "end_time", "location", "is_paid", "status"]);
    }

    public function testCreatingEventWithEndDateBeforeStartFails(): void
    {
        $user = User::factory()->create();
        $org = Organization::factory()->create();
        $org->users()->attach($user);

        Sanctum::actingAs($user);

        $payload = [
            "title" => "Invalid Time Event",
            "start_time" => now()->addDays(2)->toDateTimeString(),
            "end_time" => now()->addDay()->toDateTimeString(),
            "location" => "Test Location",
            "is_paid" => true,
            "status" => "published",
        ];

        $response = $this->postJson("/api/organizations/{$org->id}/events", $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(["end_time"]);
    }
}
