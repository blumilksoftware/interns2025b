<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Interns2025b\Models\User;
use Tests\TestCase;

class UpdatePasswordControllerTest extends TestCase
{
    public function testPasswordCanBeUpdated(): void
    {
        $user = $this->authenticatedUser();

        $response = $this->putJson("/api/auth/change-password", [
            "current_password" => "OldPassword123",
            "new_password" => "NewPassword456",
            "new_password_confirmation" => "NewPassword456",
        ]);

        $response->assertOk()
            ->assertJson([
                "message" => "Password updated successfully.",
            ]);

        $this->assertTrue(Hash::check("NewPassword456", $user->fresh()->password));
    }

    public function testPasswordUpdateFailsIfNewSameAsOld(): void
    {
        $this->authenticatedUser();

        $response = $this->putJson("/api/auth/change-password", [
            "current_password" => "OldPassword123",
            "new_password" => "OldPassword123",
            "new_password_confirmation" => "OldPassword123",
        ]);

        $response->assertStatus(422)
            ->assertJson([
                "message" => "New password cannot be the same as the current password.",
            ]);
    }

    public function testPasswordUpdateFailsWithWrongCurrentPassword(): void
    {
        $this->authenticatedUser();

        $response = $this->putJson("/api/auth/change-password", [
            "current_password" => "WrongPassword",
            "new_password" => "AnotherNewPass123",
            "new_password_confirmation" => "AnotherNewPass123",
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(["current_password"]);
    }

    public function testPasswordUpdateFailsWithUnconfirmedPassword(): void
    {
        $this->authenticatedUser();

        $response = $this->putJson("/api/auth/change-password", [
            "current_password" => "OldPassword123",
            "new_password" => "MismatchPass",
            "new_password_confirmation" => "NotSame",
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(["new_password"]);
    }

    public function testGuestCannotUpdatePassword(): void
    {
        $response = $this->putJson("/api/auth/change-password", [
            "current_password" => "OldPassword123",
            "new_password" => "NewPassword456",
            "new_password_confirmation" => "NewPassword456",
        ]);

        $response->assertUnauthorized();
    }

    public function testPasswordChangeIsThrottledAfterLimit(): void
    {
        $user = User::factory()->create([
            "password" => Hash::make("OldPassword123"),
        ]);

        $this->actingAs($user, "sanctum");

        $this->putJson("/api/auth/change-password", [
            "current_password" => "OldPassword123",
            "new_password" => "NewPassword456",
            "new_password_confirmation" => "NewPassword456",
        ])->assertStatus(200)
            ->assertJson(["message" => __("passwords.updated_successfully")]);

        $this->putJson("/api/auth/change-password", [
            "current_password" => "NewPassword456",
            "new_password" => "NewPassword789",
            "new_password_confirmation" => "NewPassword789",
        ])->assertStatus(429)
            ->assertJson(["message" => __("passwords.throttled")]);
    }

    protected function authenticatedUser(): User
    {
        $user = User::factory()->create([
            "password" => Hash::make("OldPassword123"),
        ]);

        $this->actingAs($user);

        return $user;
    }
}
