<?php

declare(strict_types=1);

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Interns2025b\Models\Event;
use Interns2025b\Models\Organization;
use Interns2025b\Models\Report;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $reporter;
    private User $targetUser;
    private Organization $organization;
    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reporter = User::factory()->create();
        $this->targetUser = User::factory()->create();
        $this->organization = Organization::factory()->create();
        $this->event = Event::factory()->create();
    }

    public function testUserCanReportAnotherUser(): void
    {
        $response = $this->actingAs($this->reporter)->postJson("/api/reports", [
            "type" => "user",
            "id" => $this->targetUser->id,
        ]);

        $response->assertStatus(Status::HTTP_OK);
        $this->assertDatabaseHas("reports", [
            "reportable_type" => User::class,
            "reportable_id" => $this->targetUser->id,
            "reporter_id" => $this->reporter->id,
        ]);
    }

    public function testUserCanReportAnOrganization(): void
    {
        $response = $this->actingAs($this->reporter)->postJson("/api/reports", [
            "type" => "organization",
            "id" => $this->organization->id,
        ]);

        $response->assertStatus(Status::HTTP_OK);
    }

    public function testUserCanReportAnEvent(): void
    {
        $response = $this->actingAs($this->reporter)->postJson("/api/reports", [
            "type" => "event",
            "id" => $this->event->id,
        ]);

        $response->assertStatus(Status::HTTP_OK);
    }

    public function testUserCannotReportSameTargetTwiceInOneDay(): void
    {
        Report::factory()->create([
            "reporter_id" => $this->reporter->id,
            "reportable_type" => User::class,
            "reportable_id" => $this->targetUser->id,
            "created_at" => now(),
        ]);

        $response = $this->actingAs($this->reporter)->postJson("/api/reports", [
            "type" => "user",
            "id" => $this->targetUser->id,
        ]);

        $response->assertStatus(Status::HTTP_TOO_MANY_REQUESTS);
    }

    public function testUserCanReportSameTargetNextDay(): void
    {
        Report::factory()->create([
            "reporter_id" => $this->reporter->id,
            "reportable_type" => User::class,
            "reportable_id" => $this->targetUser->id,
            "created_at" => Carbon::yesterday(),
        ]);

        $response = $this->actingAs($this->reporter)->postJson("/api/reports", [
            "type" => "user",
            "id" => $this->targetUser->id,
        ]);

        $response->assertStatus(Status::HTTP_OK);
    }

    public function testGuestCannotReport(): void
    {
        $response = $this->postJson("/api/reports", [
            "type" => "user",
            "id" => $this->targetUser->id,
        ]);

        $response->assertStatus(Status::HTTP_UNAUTHORIZED);
    }

    public function testInvalidTypeIsRejected(): void
    {
        $response = $this->actingAs($this->reporter)->postJson("/api/reports", [
            "type" => "invalid_type",
            "id" => 999,
        ]);

        $response->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testMissingIdIsRejected(): void
    {
        $response = $this->actingAs($this->reporter)->postJson("/api/reports", [
            "type" => "user",
        ]);

        $response->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testReasonFieldIsOptional(): void
    {
        $response = $this->actingAs($this->reporter)->postJson("/api/reports", [
            "type" => "user",
            "id" => $this->targetUser->id,
        ]);

        $response->assertStatus(Status::HTTP_OK);
    }

    public function testReasonFieldIsStored(): void
    {
        $reason = "Inappropriate content";

        $response = $this->actingAs($this->reporter)->postJson("/api/reports", [
            "type" => "organization",
            "id" => $this->organization->id,
            "reason" => $reason,
        ]);

        $response->assertStatus(Status::HTTP_OK);
        $this->assertDatabaseHas("reports", [
            "reportable_type" => Organization::class,
            "reportable_id" => $this->organization->id,
            "reporter_id" => $this->reporter->id,
            "reason" => $reason,
        ]);
    }

    public function testDifferentUsersCanReportSameTarget(): void
    {
        $anotherUser = User::factory()->create();

        $this->actingAs($this->reporter)->postJson("/api/reports", [
            "type" => "user",
            "id" => $this->targetUser->id,
        ])->assertStatus(Status::HTTP_OK);

        $this->actingAs($anotherUser)->postJson("/api/reports", [
            "type" => "user",
            "id" => $this->targetUser->id,
        ])->assertStatus(Status::HTTP_OK);
    }
}
