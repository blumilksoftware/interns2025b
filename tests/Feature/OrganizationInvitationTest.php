<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Interns2025b\Mail\OrganizationInvitationMail;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;
use Tests\TestCase;

class OrganizationInvitationTest extends TestCase
{
    protected User $owner;
    protected User $invitee;
    protected User $nonOwner;
    protected Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->owner = User::factory()->create();
        $this->invitee = User::factory()->create(["email" => "invitee@example.com"]);
        $this->nonOwner = User::factory()->create();
        $this->organization = Organization::factory()->for($this->owner, "owner")->create();
    }

    public function testOwnerCanInviteUserByEmail(): void
    {
        $this->actingAs($this->owner);

        $response = $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $this->invitee->email,
        ]);

        $response->assertStatus(200);

        Mail::assertSent(OrganizationInvitationMail::class, fn($mail) => $mail->hasTo("invitee@example.com"));
    }

    public function testNonOwnerCannotInvite(): void
    {
        $this->actingAs($this->nonOwner);

        $response = $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $this->invitee->email,
        ]);

        $response->assertForbidden();
    }

    public function testGuestCannotInvite(): void
    {
        $response = $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $this->invitee->email,
        ]);

        $response->assertUnauthorized();
    }

    public function testUserCanAcceptInvitation(): void
    {
        $url = URL::signedRoute("organizations.accept-invite", [
            "organization" => $this->organization->id,
            "email" => $this->invitee->email,
        ]);

        $this->actingAs($this->invitee);

        $response = $this->getJson($url);

        $response->assertOk();

        $this->assertTrue($this->organization->fresh()->users->contains($this->invitee));
    }

    public function testInvalidSignatureIsRejected(): void
    {
        $url = URL::temporarySignedRoute("organizations.accept-invite", now()->subMinutes(1), [
            "organization" => $this->organization->id,
            "email" => $this->invitee->email,
        ]);

        $this->actingAs($this->invitee);

        $response = $this->getJson($url);

        $response->assertStatus(403);
    }
}
