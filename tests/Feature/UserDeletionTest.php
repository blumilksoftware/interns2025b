<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Interns2025b\Mail\DeleteAccountLinkMail;
use Interns2025b\Models\User;
use Tests\TestCase;

class UserDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanRequestDeletionEmail(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $this->actingAs($user, "sanctum")
            ->postJson("/api/profile/delete-request")
            ->assertStatus(200)
            ->assertJson(["message" => "Confirmation email sent."]);

        Mail::assertSent(DeleteAccountLinkMail::class, fn($mail) => $mail->user->id === $user->id);
    }

    public function testDeletionFailsWithInvalidSignature(): void
    {
        $user = User::factory()->create();

        $url = "/api/confirm-delete/{$user->id}?signature=invalid";

        $this->getJson($url)->assertStatus(403);
        $this->assertDatabaseHas("users", ["id" => $user->id]);
    }
}
