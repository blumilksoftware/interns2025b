<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Interns2025b\Mail\DeleteAccountLinkMail;
use Interns2025b\Models\User;
use Tests\TestCase;

class UserDeletionTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Mail::fake();
    }

    public function testUserCanRequestDeletionEmail(): void
    {
        $this->actingAs($this->user, "sanctum")
            ->postJson("/api/profile/delete-request")
            ->assertStatus(200)
            ->assertJson(["message" => __("profile.email_sent")]);

        Mail::assertSent(DeleteAccountLinkMail::class, fn($mail): bool => $mail->user->id === $this->user->id);
    }

    public function testUserCannotRequestDeletionTooFrequently(): void
    {
        $this->actingAs($this->user, "sanctum")
            ->postJson("/api/profile/delete-request")
            ->assertStatus(200)
            ->assertJson(["message" => __("profile.email_sent")]);

        $this->postJson("/api/profile/delete-request")
            ->assertStatus(429)
            ->assertJson(["message" => __("profile.throttled")]);
    }

    public function testDeletionFailsWithInvalidSignature(): void
    {
        $this->deleteJson("/api/confirm-delete/{$this->user->id}?signature=invalid")
            ->assertStatus(403);

        $this->assertDatabaseHas("users", ["id" => $this->user->id]);
    }
}
