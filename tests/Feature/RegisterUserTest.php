<?php

declare(strict_types=1);

namespace Tests\Feature;

use Interns2025b\Models\User;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    public function testRegisterUserSuccessfully(): void
    {
        $response = $this->postJson("/api/auth/register", $this->validData());

        $response->assertStatus(200)
            ->assertJson([
                "message" => "success",
            ]);

        $this->assertDatabaseHas("users", [
            "email" => "jan.kowalski@gmail.com",
            "first_name" => "Jan",
            "last_name" => "Kowalski",
        ]);

        $user = User::query()->where("email", "jan.kowalski@gmail.com")->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole("user"));
    }

    public function testRegisterFailsWithExistingEmail(): void
    {
        User::factory()->create([
            "first_name" => "NotJan",
            "email" => "existing@gmail.com",
        ]);

        $response = $this->postJson("/api/auth/register", $this->validData([
            "first_name" => "Jan",
            "last_name" => "Nowak",
            "email" => "existing@gmail.com",
        ]));

        $response->assertStatus(200);

        $this->assertDatabaseHas("users", [
            "email" => "existing@gmail.com",
            "first_name" => "NotJan",
        ]);
    }

    public function testRegisterFailsWithInvalidData(): void
    {
        $response = $this->postJson("/api/auth/register", [
            "first_name" => "",
            "email" => "not-an-email",
            "password" => "short",
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(["first_name", "email", "password"]);
    }

    public function testUserCanNotRegisterWithTooLongEmail(): void
    {
        $email = str_repeat("a", 255) . "@ex.com";
        $response = $this->postJson("/api/auth/register", $this->validData(["email" => $email]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors("email");
    }

    public function testUserCanNotRegisterWithTooLongName(): void
    {
        $longName = str_repeat("a", 256);
        $response = $this->postJson("/api/auth/register", $this->validData(["first_name" => $longName]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors("first_name");
    }

    public function testUserCanNotRegisterWithTooLongSurname(): void
    {
        $longSurname = str_repeat("b", 256);
        $response = $this->postJson("/api/auth/register", $this->validData(["last_name" => $longSurname]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors("last_name");
    }

    public function testUserCanNotRegisterWithTooLongPassword(): void
    {
        $longPassword = str_repeat("p", 256);
        $response = $this->postJson("/api/auth/register", $this->validData(["password" => $longPassword]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors("password");
    }

    public function testUserCanNotRegisterWithTooShortPassword(): void
    {
        $shortPassword = str_repeat("p", 4);
        $response = $this->postJson("/api/auth/register", $this->validData(["password" => $shortPassword]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors("password");
    }

    private function validData(array $overrides = []): array
    {
        return array_merge([
            "first_name" => "Jan",
            "last_name" => "Kowalski",
            "email" => "jan.kowalski@gmail.com",
            "password" => "securePassword123",
            "password_confirmation" => "securePassword123",
        ], $overrides);
    }
}
