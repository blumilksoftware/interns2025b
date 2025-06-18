<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Interns2025b\Models\User;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    public function testUserCanRequestPasswordResetLink(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->postJson("/api/auth/forgot-password", [
            "email" => $user->email,
        ]);

        $response->assertOk()
            ->assertJson(["message" => trans("passwords.sent")]);

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
