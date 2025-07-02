<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Interns2025b\Models\Event;
use Interns2025b\Models\User;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexReturnsEvents(): void
    {
        Event::factory()->count(3)->create();

        $response = $this->getJson("/api/events");

        $response->assertOk()
            ->assertJsonCount(3, "data");
    }

    public function testStoreCreatesEvent(): void
    {
        $user = User::factory()->create();

        $payload = [
            "title" => "Test Event",
            "description" => "Test Description",
            "location" => "Test Location",
            "start_time" => now()->addDay()->toISOString(),
            "end_time" => now()->addDays(2)->toISOString(),
            "is_paid" => true,
            "status" => "published",
        ];

        $response = $this->actingAs($user)->postJson("/api/events", $payload);

        $response->assertCreated();
        $this->assertDatabaseHas("events", ["title" => "Test Event"]);
    }

    public function testShowReturnsEvent(): void
    {
        $event = Event::factory()->create();

        $response = $this->getJson("/api/events/{$event->id}");

        $response->assertOk()
            ->assertJson(["data" => ["id" => $event->id]]);
    }

    public function testOnlyOwnerCanUpdateEvent(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $event = Event::factory()->create([
            "owner_type" => get_class($owner),
            "owner_id" => $owner->id,
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

        $this->actingAs($owner)->putJson("/api/events/{$event->id}", $updatePayload)->assertOk();
        $this->actingAs($otherUser)->putJson("/api/events/{$event->id}", $updatePayload)->assertForbidden();
        $this->putJson("/api/events/{$event->id}", $updatePayload)->assertUnauthorized();
    }

    public function testOnlyOwnerCanDeleteEvent(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $event = Event::factory()->create([
            "owner_type" => get_class($owner),
            "owner_id" => $owner->id,
        ]);

        $this->actingAs($owner)->deleteJson("/api/events/{$event->id}")->assertOk();
        $this->assertDatabaseMissing("events", ["id" => $event->id]);

        $event = Event::factory()->create([
            "owner_type" => get_class($owner),
            "owner_id" => $owner->id,
        ]);

        $this->actingAs($otherUser)->deleteJson("/api/events/{$event->id}")->assertForbidden();
        $this->deleteJson("/api/events/{$event->id}")->assertUnauthorized();
    }
}
