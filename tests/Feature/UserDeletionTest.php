<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Interns2025b\Mail\DeleteAccountLinkMail;
use Interns2025b\Models\User;
use Tests\TestCase;

class UserDeletionTest extends TestCase
{
    public function testUserCanRequestDeletionEmail(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $this->actingAs($user, "sanctum")
            ->postJson("/api/profile/delete-request")
            ->assertStatus(200)
            ->assertJson(["message" => "Confirmation e-mail sent."]);

        Mail::assertSent(DeleteAccountLinkMail::class, fn($mail): bool => $mail->user->id === $user->id);
    }

    public function testDeletionFailsWithInvalidSignature(): void
    {
        $user = User::factory()->create();

        $url = "/api/confirm-delete/{$user->id}?signature=invalid";

        $this->deleteJson($url)->assertStatus(403);
        $this->assertDatabaseHas("users", ["id" => $user->id]);
    }
}
