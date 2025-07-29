<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Interns2025b\Mail\OrganizationInvitationMail;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class OrganizationInvitationTest extends TestCase
{
    protected User $owner;
    protected User $superAdmin;
    protected User $admin;
    protected User $member;
    protected User $nonMember;
    protected User $invitee;
    protected Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->owner = User::factory()->create();
        $this->superAdmin = User::factory()->superAdmin()->create();
        $this->admin = User::factory()->admin()->create();
        $this->member = User::factory()->create();
        $this->nonMember = User::factory()->create();
        $this->invitee = User::factory()->create(["email" => "invitee@example.com"]);

        $this->organization = Organization::factory()->for($this->owner, "owner")->create();

        $this->organization->users()->attach($this->member->id);
    }

    public function testOwnerCanInviteUserByEmail(): void
    {
        $this->actingAs($this->owner);

        $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $this->invitee->email,
        ])->assertOk();

        Mail::assertSent(OrganizationInvitationMail::class, fn($mail): bool => $mail->hasTo($this->invitee->email));
    }

    public function testOrganizationMemberCannotInviteUser(): void
    {
        $this->actingAs($this->member);

        $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $this->invitee->email,
        ])->assertForbidden();
    }

    public function testSuperAdminCanInviteUser(): void
    {
        $this->actingAs($this->superAdmin);

        $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $this->invitee->email,
        ])->assertOk();

        Mail::assertSent(OrganizationInvitationMail::class, fn($mail): bool => $mail->hasTo($this->invitee->email));
    }

    public function testAdminCanInviteUser(): void
    {
        $this->actingAs($this->admin);

        $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $this->invitee->email,
        ])->assertOk();

        Mail::assertSent(OrganizationInvitationMail::class, fn($mail): bool => $mail->hasTo($this->invitee->email));
    }

    public function testNonOwnerNonMemberNonSuperAdminCannotInvite(): void
    {
        $this->actingAs($this->nonMember);

        $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $this->invitee->email,
        ])->assertForbidden();
    }

    public function testGuestCannotInvite(): void
    {
        $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $this->invitee->email,
        ])->assertUnauthorized();
    }

    public function testUserCanAcceptInvitation(): void
    {
        $url = URL::signedRoute("organizations.accept-invite", [
            "organization" => $this->organization->id,
            "email" => $this->invitee->email,
        ]);

        $this->actingAs($this->invitee);

        $this->getJson($url)->assertOk();

        $this->assertTrue($this->organization->fresh()->users->contains($this->invitee));
    }

    public function testInvalidSignatureIsRejected(): void
    {
        $url = URL::temporarySignedRoute("organizations.accept-invite", now()->subMinutes(1), [
            "organization" => $this->organization->id,
            "email" => $this->invitee->email,
        ]);

        $this->actingAs($this->invitee);

        $this->getJson($url)->assertStatus(403);
    }

    public function testAcceptInvitationUnauthorizedIfUserMissing(): void
    {
        $url = URL::signedRoute("organizations.accept-invite", [
            "organization" => $this->organization->id,
            "email" => $this->invitee->email,
        ]);

        $this->getJson($url)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(["message" => __("organization.invitation_unauthorized")]);
    }

    public function testAcceptInvitationUnauthorizedIfEmailMismatch(): void
    {
        $url = URL::signedRoute("organizations.accept-invite", [
            "organization" => $this->organization->id,
            "email" => "wrongemail@example.com",
        ]);

        $this->actingAs($this->invitee);

        $this->getJson($url)
            ->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(["message" => __("organization.invitation_unauthorized")]);
    }

    public function testInvitationThrottleWorks(): void
    {
        $this->actingAs($this->owner);

        $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $this->invitee->email,
        ])->assertOk();

        $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $this->invitee->email,
        ])->assertStatus(429)
            ->assertJson(["message" => __("organization.invitation_throttled")]);
    }

    public function testUserCanInviteDifferentUsersWithoutThrottle(): void
    {
        $this->actingAs($this->owner);

        $invitee2 = User::factory()->create(["email" => "invitee2@example.com"]);
        $invitee3 = User::factory()->create(["email" => "invitee3@example.com"]);

        $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $this->invitee->email,
        ])->assertOk();

        $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $invitee2->email,
        ])->assertOk();

        $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $invitee3->email,
        ])->assertOk();
    }

    public function testUserCanInviteSameUserToDifferentOrganizationsWithoutThrottle(): void
    {
        $this->actingAs($this->owner);

        $organization2 = Organization::factory()->for($this->owner, "owner")->create();

        $this->postJson("/api/organizations/{$this->organization->id}/invite", [
            "email" => $this->invitee->email,
        ])->assertOk();

        $this->postJson("/api/organizations/{$organization2->id}/invite", [
            "email" => $this->invitee->email,
        ])->assertOk();
    }
}
