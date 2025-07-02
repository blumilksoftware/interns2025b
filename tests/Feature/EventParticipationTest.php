<?php

declare(strict_types=1);

namespace Tests\Feature;

use Interns2025b\Enums\EventStatus;
use Interns2025b\Models\Event;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;
use Tests\TestCase;

class EventParticipationTest extends TestCase
{
    public function testJoinAndLeaveEventViaApi(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(["status" => EventStatus::Published]);

        $this->actingAs($user, "sanctum");

        $response = $this->postJson("/api/events/$event->id/participate");
        $response->assertStatus(Status::HTTP_OK)
            ->assertJson(["message" => __("events.join_success")]);

        $this->assertDatabaseHas("event_user", [
            "user_id" => $user->id,
            "event_id" => $event->id,
        ]);

        $response = $this->postJson("/api/events/$event->id/participate");
        $response->assertStatus(Status::HTTP_OK)
            ->assertJson(["message" => __("events.leave_success")]);

        $this->assertDatabaseMissing("event_user", [
            "user_id" => $user->id,
            "event_id" => $event->id,
        ]);
    }

    public function testCannotJoinEndedOrCanceledEvent(): void
    {
        $user = User::factory()->create();
        $endedEvent = Event::factory()->create(["status" => EventStatus::Ended]);
        $canceledEvent = Event::factory()->create(["status" => EventStatus::Canceled]);

        $this->actingAs($user, "sanctum");

        $response = $this->postJson("/api/events/$endedEvent->id/participate");
        $response->assertStatus(Status::HTTP_FORBIDDEN)
            ->assertJson(["message" => __("events.cannot_join")]);

        $response = $this->postJson("/api/events/$canceledEvent->id/participate");
        $response->assertStatus(Status::HTTP_FORBIDDEN)
            ->assertJson(["message" => __("events.cannot_join")]);
    }

    public function testCannotJoinNonexistentEvent(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, "sanctum");

        $response = $this->postJson("/api/events/999999/participation");
        $response->assertStatus(Status::HTTP_NOT_FOUND);
    }
}
