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
    protected User $superAdmin;
    protected Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->owner = User::factory()->create();
        $this->superAdmin = User::factory()->superAdmin()->create();
        $this->organization = Organization::factory()->for($this->owner, "owner")->create();
    }

    public function testOwnerCanInviteUserByEmail(): void
    {
        $invitee = User::factory()->create(["email" => "invitee@example.com"]);
        $this->actingAs($this->owner);

        $response = $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $invitee->email,
        ]);

        $response->assertOk();
        Mail::assertSent(OrganizationInvitationMail::class, fn($mail) => $mail->hasTo($invitee->email));
    }

    public function testOrganizationMemberCannotInviteUser(): void
    {
        $member = User::factory()->create();
        $invitee = User::factory()->create(["email" => "invitee@example.com"]);

        $this->organization->users()->attach($member->id);
        $this->actingAs($member);

        $response = $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $invitee->email,
        ]);

        $response->assertForbidden();
    }

    public function testSuperAdminCanInviteUser(): void
    {
        $invitee = User::factory()->create(["email" => "invitee@example.com"]);

        $this->actingAs($this->superAdmin);

        $response = $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $invitee->email,
        ]);

        $response->assertOk();
        Mail::assertSent(OrganizationInvitationMail::class, fn($mail) => $mail->hasTo($invitee->email));
    }

    public function testAdminCanInviteUser(): void
    {
        $admin = User::factory()->admin()->create();
        $invitee = User::factory()->create(["email" => "invitee@example.com"]);

        $this->actingAs($admin);

        $response = $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $invitee->email,
        ]);

        $response->assertOk();
        Mail::assertSent(OrganizationInvitationMail::class, fn($mail) => $mail->hasTo($invitee->email));
    }

    public function testNonOwnerNonMemberNonSuperAdminCannotInvite(): void
    {
        $nonOwner = User::factory()->create();
        $invitee = User::factory()->create(["email" => "invitee@example.com"]);

        $this->actingAs($nonOwner);

        $response = $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $invitee->email,
        ]);

        $response->assertForbidden();
    }

    public function testGuestCannotInvite(): void
    {
        $invitee = User::factory()->create(["email" => "invitee@example.com"]);

        $response = $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $invitee->email,
        ]);

        $response->assertUnauthorized();
    }

    public function testUserCanAcceptInvitation(): void
    {
        $invitee = User::factory()->create(["email" => "invitee@example.com"]);

        $url = URL::signedRoute("organizations.accept-invite", [
            "organization" => $this->organization->id,
            "email" => $invitee->email,
        ]);

        $this->actingAs($invitee);

        $response = $this->getJson($url);

        $response->assertOk();
        $this->assertTrue($this->organization->fresh()->users->contains($invitee));
    }

    public function testInvalidSignatureIsRejected(): void
    {
        $invitee = User::factory()->create(["email" => "invitee@example.com"]);

        $url = URL::temporarySignedRoute("organizations.accept-invite", now()->subMinutes(1), [
            "organization" => $this->organization->id,
            "email" => $invitee->email,
        ]);

        $this->actingAs($invitee);

        $response = $this->getJson($url);

        $response->assertStatus(403);
    }
}
