<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;
use Tests\TestCase;

class LoginUserTest extends TestCase
{
    public function testUserCanLoginSuccessfully(): void
    {
        $password = "securePassword123";

        User::factory()->create([
            "email" => "jan.kowalski@gmail.com",
            "password" => Hash::make($password),
        ]);

        $response = $this->postJson("/api/auth/login", $this->validData());

        $response->assertStatus(Status::HTTP_OK)
            ->assertJsonStructure([
                "message",
                "token",
                "user" => [
                    "id",
                    "first_name",
                    "last_name",
                    "email",
                    "email_verified_at",
                    "created_at",
                    "updated_at",
                ],
            ])
            ->assertJson([
                "message" => "success",
            ]);
    }

    public function testUserCannotLoginWithWrongCredentials(): void
    {
        User::factory()->create([
            "email" => "jan.kowalski@gmail.com",
            "password" => Hash::make("correctPassword"),
        ]);

        $response = $this->postJson("/api/auth/login", $this->validData(["password" => "wrongPassword"]));

        $response->assertStatus(Status::HTTP_FORBIDDEN)
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

        $response->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY);
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
