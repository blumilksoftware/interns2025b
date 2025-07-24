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
    protected User $admin;
    protected User $superadmin;
    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->owner = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->admin = User::factory()->admin()->create();
        $this->superadmin = User::factory()->superAdmin()->create();

        $this->event = Event::factory()->create([
            "owner_type" => get_class($this->owner),
            "owner_id" => $this->owner->id,
        ]);
    }

    public function testIndexReturnsPaginatedEvents(): void
    {
        Event::factory()->count(20)->create();

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
        Event::factory()->count(12)->create();

        $response = $this->getJson("/api/events?per_page=5");

        $response->assertStatus(Http::HTTP_OK);
        $this->assertCount(5, $response->json("data"));
        $this->assertEquals(3, $response->json("meta.last_page"));
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
            "start_time" => now()->addDay()->toISOString(),
            "end_time" => now()->addDays(2)->toISOString(),
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
            "start_time" => now()->addDay()->toISOString(),
            "end_time" => now()->addDays(2)->toISOString(),
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
            "start_time" => now()->addDay()->toISOString(),
            "end_time" => now()->addDays(2)->toISOString(),
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

        $response->assertStatus(Http::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(["start_time"]);
    }

    public function testUserCannotCreateSecondPublishedOrOngoingEvent(): void
    {
        $this->actingAs($this->user);
        $this->createPublishedEventForUser($this->user);

        $payload = Event::factory()->make([
            "status" => "ongoing",
            "start_time" => now()->addHour(),
            "end_time" => now()->addHours(2),
        ])->toArray();

        $payload["start_time"] = now()->addHour()->toISOString();
        $payload["end_time"] = now()->addHours(2)->toISOString();

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
                "start_time" => now()->addDay(),
                "end_time" => now()->addDays(2),
            ])->toArray();

            $payload["start_time"] = now()->addDay()->toISOString();
            $payload["end_time"] = now()->addDays(2)->toISOString();

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

    private function createPublishedEventForUser(User $user): Event
    {
        return Event::factory()->create([
            "owner_id" => $user->id,
            "owner_type" => get_class($user),
            "status" => "published",
        ]);
    }
}
