<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Interns2025b\Models\User;
use Tests\TestCase;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanLoginSuccessfully(): void
    {
        $password = "securePassword123";

        User::factory()->create([
            "email" => "jan.kowalski@gmail.com",
            "password" => bcrypt($password),
        ]);

        $response = $this->postJson("/api/auth/login", $this->validData());

        $response->assertStatus(200)
            ->assertJsonStructure([
                "message",
                "token",
                "user_id",
            ])
            ->assertJson([
                "message" => "success",
            ]);
    }

    public function testUserCannotLoginWithWrongCredentials(): void
    {
        User::factory()->create([
            "email" => "jan.kowalski@gmail.com",
            "password" => bcrypt("correctPassword"),
        ]);

        $response = $this->postJson("/api/auth/login", $this->validData(["password" => "wrongPassword"]));

        $response->assertStatus(403)
            ->assertJson([
                "message" => __("auth.failed"),
            ]);
    }

    public function testUserCannotLoginWithInvalidData(): void
    {
        $response = $this->postJson("/api/auth/login", [
            "email" => "not-an-email",
            "password" => "",
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(["email", "password"]);
    }

    private function validData(array $overrides = []): array
    {
        return array_merge([
            "email" => "jan.kowalski@gmail.com",
            "password" => "securePassword123",
        ], $overrides);
    }
}
