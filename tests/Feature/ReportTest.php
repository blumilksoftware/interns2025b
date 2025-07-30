<?php

declare(strict_types=1);

namespace Tests\Feature;

use Carbon\Carbon;
use Interns2025b\Models\Event;
use Interns2025b\Models\Organization;
use Interns2025b\Models\Report;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;
use Tests\TestCase;

class ReportTest extends TestCase
{
    private User $reporter;
    private User $targetUser;
    private User $anotherUser;
    private Organization $organization;
    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reporter = User::factory()->admin()->create();
        $this->targetUser = User::factory()->create();
        $this->anotherUser = User::factory()->create();
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
            "id" => 123,
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
        $reason = "Violation";

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
        $this->actingAs($this->reporter)->postJson("/api/reports", [
            "type" => "user",
            "id" => $this->targetUser->id,
        ])->assertStatus(Status::HTTP_OK);

        $this->actingAs($this->anotherUser)->postJson("/api/reports", [
            "type" => "user",
            "id" => $this->targetUser->id,
        ])->assertStatus(Status::HTTP_OK);
    }

    public function testUserCanViewUserReports(): void
    {
        Report::factory()->create([
            "reportable_type" => User::class,
            "reportable_id" => $this->targetUser->id,
            "reporter_id" => $this->reporter->id,
        ]);

        $response = $this->actingAs($this->reporter)->getJson("/api/admin/reports/users");

        $response->assertStatus(Status::HTTP_OK)
            ->assertJsonStructure([
                "data" => [["id", "created_at", "reason", "reportable_type", "reportable_id"]],
            ])
            ->assertJsonFragment(["reportable_type" => "User"]);
    }

    public function testUserCanViewOrganizationReports(): void
    {
        Report::factory()->create([
            "reportable_type" => Organization::class,
            "reportable_id" => $this->organization->id,
            "reporter_id" => $this->reporter->id,
        ]);

        $response = $this->actingAs($this->reporter)->getJson("/api/admin/reports/organizations");

        $response->assertStatus(Status::HTTP_OK)
            ->assertJsonStructure([
                "data" => [["id", "created_at", "reason", "reportable_type", "reportable_id"]],
            ])
            ->assertJsonFragment(["reportable_type" => "Organization"]);
    }

    public function testUserCanViewEventReports(): void
    {
        Report::factory()->create([
            "reportable_type" => Event::class,
            "reportable_id" => $this->event->id,
            "reporter_id" => $this->reporter->id,
        ]);

        $response = $this->actingAs($this->reporter)->getJson("/api/admin/reports/events");

        $response->assertStatus(Status::HTTP_OK)
            ->assertJsonStructure([
                "data" => [["id", "created_at", "reason", "reportable_type", "reportable_id"]],
            ])
            ->assertJsonFragment(["reportable_type" => "Event"]);
    }
}
