<?php

declare(strict_types=1);

namespace Tests\Feature;

use Interns2025b\Models\Event;
use Interns2025b\Models\User;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    protected User $user;
    protected User $owner;
    protected User $otherUser;
    protected User $admin;
    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->owner = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->admin = User::factory()->admin()->create();
        $this->superadmin = User::factory()->superAdmin()->create();
    }

    public function testIndexReturnsPaginatedEvents(): void
    {
        Event::factory()->count(20)->create();

        $response = $this->getJson("/api/events");

        $response->assertOk()
            ->assertJsonStructure([
                "data",
                "links" => ["first", "last", "prev", "next"],
                "meta" => ["current_page", "last_page", "per_page", "total"],
            ]);

        $this->assertCount(10, $response->json("data"));
    }

    public function testIndexRespectsPerPageParameter(): void
    {
        Event::factory()->count(12)->create();

        $response = $this->getJson("/api/events?per_page=5");

        $response->assertOk();
        $this->assertCount(5, $response->json("data"));
        $this->assertEquals(3, $response->json("meta.last_page"));
    }

    public function testShowReturnsEvent(): void
    {
        $event = Event::factory()->create();

        $this->getJson("/api/events/{$event->id}")->assertOk();
        $this->actingAs($this->user)->getJson("/api/events/{$event->id}")->assertOk();
    }

    public function testOnlyOwnerCanUpdateEvent(): void
    {
        $event = Event::factory()->create([
            "owner_type" => get_class($this->owner),
            "owner_id" => $this->owner->id,
        ]);

        $updatePayload = [
            "title" => "Updated Title",
            "description" => "Updated Description",
            "location" => "Updated Location",
            "start_time" => now()->addDay()->toISOString(),
            "end_time" => now()->addDays(2)->toISOString(),
            "is_paid" => false,
            "status" => "draft",
        ];

        $this->actingAs($this->owner)->putJson("/api/events/{$event->id}", $updatePayload)->assertOk();
        $this->actingAs($this->otherUser)->putJson("/api/events/{$event->id}", $updatePayload)->assertForbidden();
        $this->putJson("/api/events/{$event->id}", $updatePayload)->assertForbidden();
    }

    public function testOnlyOwnerCanDeleteEvent(): void
    {
        $event = Event::factory()->create([
            "owner_type" => get_class($this->owner),
            "owner_id" => $this->owner->id,
        ]);

        $this->actingAs($this->owner)->deleteJson("/api/events/{$event->id}")->assertOk();
        $this->assertDatabaseMissing("events", ["id" => $event->id]);

        $event = Event::factory()->create([
            "owner_type" => get_class($this->owner),
            "owner_id" => $this->owner->id,
        ]);

        $this->actingAs($this->otherUser)->deleteJson("/api/events/{$event->id}")->assertForbidden();
        $this->deleteJson("/api/events/{$event->id}")->assertForbidden();
    }

    public function testAdminCanUpdateAnyEvent(): void
    {
        $event = Event::factory()->create([
            "owner_type" => get_class($this->owner),
            "owner_id" => $this->owner->id,
        ]);

        $payload = [
            "title" => "Admin Updated",
            "description" => "Changed",
            "location" => "New Loc",
            "start_time" => now()->addDay()->toISOString(),
            "end_time" => now()->addDays(2)->toISOString(),
            "is_paid" => false,
            "status" => "draft",
        ];

        $this->actingAs($this->admin)->putJson("/api/events/{$event->id}", $payload)->assertOk();
        $this->assertDatabaseHas("events", ["id" => $event->id, "title" => "Admin Updated"]);
    }

    public function testSuperadminCanDeleteAnyEvent(): void
    {
        $event = Event::factory()->create([
            "owner_type" => get_class($this->owner),
            "owner_id" => $this->owner->id,
        ]);

        $this->actingAs($this->superadmin)->deleteJson("/api/events/{$event->id}")->assertOk();
        $this->assertDatabaseMissing("events", ["id" => $event->id]);
    }

    public function testGuestCannotCreateEvent(): void
    {
        $payload = [
            "title" => "Unauthorized Event",
            "description" => "Guest try",
            "location" => "Somewhere",
            "start_time" => now()->addDay()->toISOString(),
            "end_time" => now()->addDays(2)->toISOString(),
            "is_paid" => true,
            "status" => "published",
        ];

        $this->postJson("/api/events", $payload)->assertUnauthorized();
    }

    public function testInvalidPayloadFailsValidation(): void
    {
        $payload = [
            "title" => "",
            "description" => "Missing other fields",
        ];

        $response = $this->actingAs($this->user)->postJson("/api/events", $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(["title", "location", "start_time", "end_time", "is_paid", "status"]);
    }

    public function testStartTimeMustBeBeforeEndTime(): void
    {
        $payload = [
            "title" => "Invalid Time",
            "description" => "Invalid time order",
            "location" => "Here",
            "start_time" => now()->addDays(3)->toISOString(),
            "end_time" => now()->addDay()->toISOString(),
            "is_paid" => false,
            "status" => "draft",
        ];

        $response = $this->actingAs($this->user)->postJson("/api/events", $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(["start_time"]);
    }

    public function testUserCannotCreateSecondPublishedOrOngoingEvent(): void
    {
        $user = $this->user;
        $this->actingAs($user);

        Event::factory()->create([
            "owner_id" => $user->id,
            "owner_type" => get_class($user),
            "status" => "published",
        ]);

        $payload = Event::factory()->make([
            "status" => "ongoing",
            "start_time" => now()->addHour(),
            "end_time" => now()->addHours(2),
        ])->toArray();

        $payload["start_time"] = now()->addHour()->toISOString();
        $payload["end_time"] = now()->addHours(2)->toISOString();

        $response = $this->postJson("/api/events", $payload);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(["status"]);
    }

    public function testUserCanCreateDraftOrEndedEvenIfTheyHavePublishedEvent(): void
    {
        $user = $this->user;
        $this->actingAs($user);

        Event::factory()->create([
            "owner_id" => $user->id,
            "owner_type" => get_class($user),
            "status" => "published",
        ]);

        foreach (["draft", "ended"] as $status) {
            $payload = Event::factory()->make([
                "status" => $status,
                "start_time" => now()->addDay(),
                "end_time" => now()->addDays(2),
            ])->toArray();

            $payload["start_time"] = now()->addDay()->toISOString();
            $payload["end_time"] = now()->addDays(2)->toISOString();

            $response = $this->postJson("/api/events", $payload);

            $response->assertCreated();
        }
    }

    public function testUserCanUpdateWithoutChangingStatusEvenIfActiveEventExists(): void
    {
        $user = $this->user;
        $this->actingAs($user);

        Event::factory()->create([
            "owner_id" => $user->id,
            "owner_type" => get_class($user),
            "status" => "published",
        ]);

        $eventToUpdate = Event::factory()->create([
            "owner_id" => $user->id,
            "owner_type" => get_class($user),
            "status" => "draft",
        ]);

        $response = $this->putJson("/api/events/{$eventToUpdate->id}", [
            "title" => "Updated Title",
        ]);

        $response->assertOk();
    }

    public function testUserCanChangeStatusToDraftOrEndedEvenIfAnotherActiveEventExists(): void
    {
        $user = $this->user;
        $this->actingAs($user);

        Event::factory()->create([
            "owner_id" => $user->id,
            "owner_type" => get_class($user),
            "status" => "published",
        ]);

        $eventToUpdate = Event::factory()->create([
            "owner_id" => $user->id,
            "owner_type" => get_class($user),
            "status" => "draft",
        ]);

        foreach (["draft", "ended"] as $newStatus) {
            $response = $this->putJson("/api/events/{$eventToUpdate->id}", [
                "status" => $newStatus,
            ]);

            $response->assertOk();
        }
    }

    public function testUserCannotChangeEventToPublishedOrOngoingIfAnotherActiveEventExists(): void
    {
        $user = $this->user;
        $this->actingAs($user);

        Event::factory()->create([
            "owner_id" => $user->id,
            "owner_type" => get_class($user),
            "status" => "published",
        ]);

        $eventToUpdate = Event::factory()->create([
            "owner_id" => $user->id,
            "owner_type" => get_class($user),
            "status" => "draft",
        ]);

        foreach (["published", "ongoing"] as $newStatus) {
            $response = $this->putJson("/api/events/{$eventToUpdate->id}", [
                "status" => $newStatus,
            ]);

            $response->assertUnprocessable();
            $response->assertJsonValidationErrors(["status"]);
        }
    }
}
