<?php

declare(strict_types=1);

namespace Tests\Feature;

use Interns2025b\Models\Event;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Http;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    protected User $user;
    protected User $owner;
    protected User $otherUser;
    protected User $urbanLegend;
    protected User $admin;
    protected User $superadmin;
    protected Event $event;
    protected array $events;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->owner = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->urbanLegend = User::factory()->urbanLegend()->create();
        $this->admin = User::factory()->admin()->create();
        $this->superadmin = User::factory()->superAdmin()->create();

        $this->event = Event::factory()->create([
            "owner_type" => get_class($this->owner),
            "owner_id" => $this->owner->id,
        ]);

        $this->events = Event::factory()->count(20)->create()->all();
    }

    public function testIndexReturnsPaginatedEvents(): void
    {
        $response = $this->getJson("/api/events");

        $response->assertStatus(Http::HTTP_OK)->assertJsonStructure([
            "data",
            "links" => ["first", "last", "prev", "next"],
            "meta" => ["current_page", "last_page", "per_page", "total"],
        ]);

        $this->assertCount(10, $response->json("data"));
    }

    public function testIndexRespectsPerPageParameter(): void
    {
        $response = $this->getJson("/api/events?per_page=5");

        $response->assertStatus(Http::HTTP_OK);
        $this->assertCount(5, $response->json("data"));
        $this->assertEquals(5, $response->json("meta.last_page"));
    }

    public function testShowReturnsEvent(): void
    {
        $this->getJson("/api/events/{$this->event->id}")->assertStatus(Http::HTTP_OK);
        $this->actingAs($this->user)->getJson("/api/events/{$this->event->id}")->assertStatus(Http::HTTP_OK);
    }

    public function testOnlyOwnerCanUpdateEvent(): void
    {
        $payload = [
            "title" => "Updated Title",
            "description" => "Updated Description",
            "location" => "Updated Location",
            "start" => now()->addDay()->toISOString(),
            "end" => now()->addDays(2)->toISOString(),
            "is_paid" => false,
            "status" => "draft",
        ];

        $this->actingAs($this->owner)->putJson("/api/events/{$this->event->id}", $payload)->assertStatus(Http::HTTP_OK);
        $this->actingAs($this->otherUser)->putJson("/api/events/{$this->event->id}", $payload)->assertStatus(Http::HTTP_FORBIDDEN);
        $this->putJson("/api/events/{$this->event->id}", $payload)->assertStatus(Http::HTTP_FORBIDDEN);
    }

    public function testOnlyOwnerCanDeleteEvent(): void
    {
        $this->actingAs($this->owner)->deleteJson("/api/events/{$this->event->id}")->assertStatus(Http::HTTP_OK);
        $this->assertDatabaseMissing("events", ["id" => $this->event->id]);

        $event = Event::factory()->create([
            "owner_type" => get_class($this->owner),
            "owner_id" => $this->owner->id,
        ]);

        $this->actingAs($this->otherUser)->deleteJson("/api/events/{$event->id}")->assertStatus(Http::HTTP_FORBIDDEN);
        $this->deleteJson("/api/events/{$event->id}")->assertStatus(Http::HTTP_FORBIDDEN);
    }

    public function testAdminCanUpdateAnyEvent(): void
    {
        $payload = [
            "title" => "Admin Updated",
            "description" => "Changed",
            "location" => "New Loc",
            "start" => now()->addDay()->toISOString(),
            "end" => now()->addDays(2)->toISOString(),
            "is_paid" => false,
            "status" => "draft",
        ];

        $this->actingAs($this->admin)->putJson("/api/events/{$this->event->id}", $payload)->assertStatus(Http::HTTP_OK);
        $this->assertDatabaseHas("events", ["id" => $this->event->id, "title" => "Admin Updated"]);
    }

    public function testSuperadminCanDeleteAnyEvent(): void
    {
        $this->actingAs($this->superadmin)->deleteJson("/api/events/{$this->event->id}")->assertStatus(Http::HTTP_OK);
        $this->assertDatabaseMissing("events", ["id" => $this->event->id]);
    }

    public function testGuestCannotCreateEvent(): void
    {
        $payload = [
            "title" => "Unauthorized Event",
            "description" => "Guest try",
            "location" => "Somewhere",
            "start" => now()->addDay()->toISOString(),
            "end" => now()->addDays(2)->toISOString(),
            "is_paid" => true,
            "status" => "published",
        ];

        $this->postJson("/api/events", $payload)->assertStatus(Http::HTTP_UNAUTHORIZED);
    }

    public function testInvalidPayloadFailsValidation(): void
    {
        $payload = [
            "title" => "",
            "description" => "Missing other fields",
        ];

        $response = $this->actingAs($this->user)->postJson("/api/events", $payload);

        $response->assertStatus(Http::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(["title", "location", "start", "end", "is_paid", "status"]);
    }

    public function testStartTimeMustBeBeforeEndTime(): void
    {
        $payload = [
            "title" => "Invalid Time",
            "description" => "Invalid time order",
            "location" => "Here",
            "start" => now()->addDays(3)->toISOString(),
            "end" => now()->addDay()->toISOString(),
            "is_paid" => false,
            "status" => "draft",
        ];

        $response = $this->actingAs($this->user)->postJson("/api/events", $payload);

        $response->assertStatus(Http::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(["start"]);
    }

    public function testUserCannotCreateSecondPublishedOrOngoingEvent(): void
    {
        $this->actingAs($this->user);
        $this->createPublishedEventForUser($this->user);

        $payload = Event::factory()->make([
            "status" => "ongoing",
            "start" => now()->addHour(),
            "end" => now()->addHours(2),
        ])->toArray();

        $payload["start"] = now()->addHour()->toISOString();
        $payload["end"] = now()->addHours(2)->toISOString();

        $response = $this->postJson("/api/events", $payload);

        $response->assertStatus(Http::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(["status"]);
    }

    public function testUserCanCreateDraftOrEndedEvenIfTheyHavePublishedEvent(): void
    {
        $this->actingAs($this->user);
        $this->createPublishedEventForUser($this->user);

        foreach (["draft", "ended"] as $status) {
            $payload = Event::factory()->make([
                "status" => $status,
                "start" => now()->addDay(),
                "end" => now()->addDays(2),
            ])->toArray();

            $payload["start"] = now()->addDay()->toISOString();
            $payload["end"] = now()->addDays(2)->toISOString();

            $this->postJson("/api/events", $payload)->assertStatus(Http::HTTP_CREATED);
        }
    }

    public function testUserCanUpdateWithoutChangingStatusEvenIfActiveEventExists(): void
    {
        $this->actingAs($this->user);
        $this->createPublishedEventForUser($this->user);

        $eventToUpdate = Event::factory()->create([
            "owner_id" => $this->user->id,
            "owner_type" => get_class($this->user),
            "status" => "draft",
        ]);

        $response = $this->putJson("/api/events/{$eventToUpdate->id}", [
            "title" => "Updated Title",
        ]);

        $response->assertStatus(Http::HTTP_OK);
    }

    public function testUserCanChangeStatusToDraftOrEndedEvenIfAnotherActiveEventExists(): void
    {
        $this->actingAs($this->user);
        $this->createPublishedEventForUser($this->user);

        $eventToUpdate = Event::factory()->create([
            "owner_id" => $this->user->id,
            "owner_type" => get_class($this->user),
            "status" => "draft",
        ]);

        foreach (["draft", "ended"] as $newStatus) {
            $response = $this->putJson("/api/events/{$eventToUpdate->id}", [
                "status" => $newStatus,
            ]);

            $response->assertStatus(Http::HTTP_OK);
        }
    }

    public function testUserCannotChangeEventToPublishedOrOngoingIfAnotherActiveEventExists(): void
    {
        $this->actingAs($this->user);
        $this->createPublishedEventForUser($this->user);

        $eventToUpdate = Event::factory()->create([
            "owner_id" => $this->user->id,
            "owner_type" => get_class($this->user),
            "status" => "draft",
        ]);

        foreach (["published", "ongoing"] as $newStatus) {
            $response = $this->putJson("/api/events/{$eventToUpdate->id}", [
                "status" => $newStatus,
            ]);

            $response->assertStatus(Http::HTTP_UNPROCESSABLE_ENTITY);
            $response->assertJsonValidationErrors(["status"]);
        }
    }

    public function testUserWithBadgeCanCreateMultiplePublishedEvents(): void
    {
        $this->actingAs($this->urbanLegend);

        Event::factory()->create([
            "owner_id" => $this->urbanLegend->id,
            "owner_type" => get_class($this->urbanLegend),
            "status" => "published",
        ]);

        $payload = Event::factory()->make([
            "status" => "published",
            "start_time" => now()->addHours(2),
            "end_time" => now()->addHours(5),
        ])->toArray();

        $payload["start_time"] = now()->addHours(2)->toISOString();
        $payload["end_time"] = now()->addHours(5)->toISOString();

        $this->postJson("/api/events", $payload)->assertCreated();
    }

    public function testUserWithBadgeCanCreateMultipleOngoingEvents(): void
    {
        $this->actingAs($this->urbanLegend);

        Event::factory()->create([
            "owner_id" => $this->urbanLegend->id,
            "owner_type" => get_class($this->urbanLegend),
            "status" => "ongoing",
        ]);

        $payload = Event::factory()->make([
            "status" => "ongoing",
            "start_time" => now()->addMinutes(10),
            "end_time" => now()->addHours(1),
        ])->toArray();

        $payload["start_time"] = now()->addMinutes(10)->toISOString();
        $payload["end_time"] = now()->addHours(1)->toISOString();

        $this->postJson("/api/events", $payload)->assertCreated();
    }

    public function testUserWithoutBadgeCannotCreateMultiplePublishedEvents(): void
    {
        $this->actingAs($this->user);

        Event::factory()->create([
            "owner_id" => $this->user->id,
            "owner_type" => get_class($this->user),
            "status" => "published",
        ]);

        $payload = Event::factory()->make([
            "status" => "published",
            "start_time" => now()->addDay(),
            "end_time" => now()->addDays(2),
        ])->toArray();

        $payload["start_time"] = now()->addDay()->toISOString();
        $payload["end_time"] = now()->addDays(2)->toISOString();

        $this->postJson("/api/events", $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(["status"]);
    }

    public function testUserWithBadgeCanUpdateEventToPublishedDespiteOtherActive(): void
    {
        $this->actingAs($this->urbanLegend);

        Event::factory()->create([
            "owner_id" => $this->urbanLegend->id,
            "owner_type" => get_class($this->urbanLegend),
            "status" => "ongoing",
        ]);

        $event = Event::factory()->create([
            "owner_id" => $this->urbanLegend->id,
            "owner_type" => get_class($this->urbanLegend),
            "status" => "draft",
        ]);

        $response = $this->putJson("/api/events/{$event->id}", [
            "status" => "published",
        ]);

        $response->assertOk();
    }

    public function testUserWithBadgeCanUpdateEventToOngoingDespiteOtherActive(): void
    {
        $this->actingAs($this->urbanLegend);

        Event::factory()->create([
            "owner_id" => $this->urbanLegend->id,
            "owner_type" => get_class($this->urbanLegend),
            "status" => "published",
        ]);

        $event = Event::factory()->create([
            "owner_id" => $this->urbanLegend->id,
            "owner_type" => get_class($this->urbanLegend),
            "status" => "draft",
        ]);

        $response = $this->putJson("/api/events/{$event->id}", [
            "status" => "ongoing",
        ]);

        $response->assertOk();
    }

    private function createPublishedEventForUser(User $user): Event
    {
        return Event::factory()->create([
            "owner_id" => $user->id,
            "owner_type" => get_class($user),
            "status" => "published",
        ]);
    }
}
