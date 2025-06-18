<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Interns2025b\Models\User;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    public function testUserCanResetPasswordWithValidToken(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            "password" => bcrypt("old-password"),
        ]);

        $this->postJson("/api/auth/forgot-password", [
            "email" => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token): bool {
            $token = $notification->token;

            return true;
        });

        $response = $this->postJson("/api/auth/reset-password", [
            "email" => $user->email,
            "token" => $token,
            "password" => "new-secure-password",
            "password_confirmation" => "new-secure-password",
        ]);

        $response->assertOk()
            ->assertJson(["message" => trans("passwords.reset")]);

        $this->assertTrue(Hash::check("new-secure-password", $user->fresh()->password));
    }

    public function testCannotResetWithInvalidToken(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson("/api/auth/reset-password", [
            "email" => $user->email,
            "token" => "fake-invalid-token",
            "password" => "something-new",
            "password_confirmation" => "something-new",
        ]);

        $response->assertStatus(400)
            ->assertJson(["message" => trans(Password::INVALID_TOKEN)]);
    }
}
