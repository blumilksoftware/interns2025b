<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Interns2025b\Enums\Role as RoleEnum;
use Interns2025b\Models\Event;
use Interns2025b\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            RoleEnum::User->value,
            RoleEnum::Administrator->value,
            RoleEnum::SuperAdministrator->value,
        ] as $role) {
            Role::findOrCreate($role, "web");
        }
    }

    public function testAnyoneCanViewIndexAndShow(): void
    {
        $event = Event::factory()->create();

        $this->getJson("/api/events")->assertOk()->assertJsonStructure([
            "*" => ["id", "title", "description", "start", "end", "location", "address", "latitude", "longitude", "is_paid", "price", "status", "image_url", "age_category", "owner_type", "owner_id"],
        ]);
        $this->getJson("/api/events/{$event->id}")->assertOk()->assertJsonFragment([
            "id" => $event->id,
            "title" => $event->title,
        ]);
    }

    public function testGuestCannotCreateEvent(): void
    {
        $this->postJson("/api/events", [])->assertUnauthorized();
    }

    public function testLoggedInUserCanCreateEvent(): void
    {
        $user = $this->createUserWithRole(RoleEnum::User->value);

        $response = $this->actingAs($user)->postJson("/api/events", [
            "title" => "Test Event",
            "description" => "Description here",
            "start" => now()->addDay()->toISOString(),
            "end" => now()->addDays(2)->toISOString(),
            "location" => "Somewhere",
            "address" => "123 Street",
            "latitude" => 1.2345678,
            "longitude" => 9.8765432,
            "is_paid" => true,
            "price" => 100.00,
            "status" => "draft",
            "image_url" => "https://example.com/image.png",
            "age_category" => "18+",
            "owner_type" => get_class($user),
            "owner_id" => $user->id,
        ]);

        $response->assertStatus(201);
        $responseData = $response->json();
        $this->assertEquals("Test Event", $responseData["title"]);
    }

    public function testOnlyOwnerOrAdminCanUpdateEvent(): void
    {
        $owner = $this->createUserWithRole(RoleEnum::User->value);
        $admin = $this->createUserWithRole(RoleEnum::Administrator->value);
        $otherUser = $this->createUserWithRole(RoleEnum::User->value);

        $event = $this->createEventForUser($owner);

        $this->actingAs($owner)->putJson("/api/events/{$event->id}", [
            "title" => "Updated Title",
        ])->assertOk();

        $this->actingAs($admin)->putJson("/api/events/{$event->id}", [
            "title" => "Admin Title",
        ])->assertOk();

        $this->actingAs($otherUser)->putJson("/api/events/{$event->id}", [
            "title" => "Not allowed",
        ])->assertForbidden();
    }

    public function testOnlyOwnerOrAdminCanDeleteEvent(): void
    {
        $owner = $this->createUserWithRole(RoleEnum::User->value);
        $superadmin = $this->createUserWithRole(RoleEnum::SuperAdministrator->value);
        $otherUser = $this->createUserWithRole(RoleEnum::User->value);

        $event = $this->createEventForUser($owner);
        $this->actingAs($owner)->deleteJson("/api/events/{$event->id}")->assertOk();

        $event = $this->createEventForUser($owner);
        $this->actingAs($superadmin)->deleteJson("/api/events/{$event->id}")->assertOk();

        $event = $this->createEventForUser($owner);
        $this->actingAs($otherUser)->deleteJson("/api/events/{$event->id}")->assertForbidden();
    }

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function createEventForUser(User $user): Event
    {
        return Event::factory()->create([
            "owner_type" => get_class($user),
            "owner_id" => $user->id,
        ]);
    }
}
