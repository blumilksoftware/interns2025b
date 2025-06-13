<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Interns2025b\Models\User;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    public function testRegisterUserSuccessfully(): void
    {
        $response = $this->postJson("/api/auth/register", $this->validData());

        $response->assertStatus(200)
            ->assertJson([
                "message" => "success",
            ]);

        $this->assertDatabaseHas("users", [
            "email" => "jan.kowalski@gmail.com",
            "firstname" => "Jan",
            "surname" => "Kowalski",
        ]);

        $user = User::where("email", "jan.kowalski@gmail.com")->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole("user"));
    }

    public function testRegisterFailsWithExistingEmail(): void
    {
        User::factory()->create([
            "firstname" => "NotJan",
            "email" => "existing@gmail.com",
        ]);

        $response = $this->postJson("/api/auth/register", $this->validData([
            "firstname" => "Jan",
            "surname" => "Nowak",
            "email" => "existing@gmail.com",
        ]));

        $response->assertStatus(200);

        $this->assertDatabaseHas("users", [
            "email" => "existing@gmail.com",
            "firstname" => "NotJan",
        ]);
    }

    public function testRegisterFailsWithInvalidData(): void
    {
        $response = $this->postJson("/api/auth/register", [
            "firstname" => "",
            "surname" => "",
            "email" => "not-an-email",
            "password" => "short",
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(["firstname", "surname", "email", "password"]);
    }

    public function testUserCanNotRegisterWithTooLongEmail(): void
    {
        $email = str_repeat("a", 255) . "@ex.com";
        $response = $this->postJson("/api/auth/register", $this->validData(["email" => $email]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors("email");
    }

    public function testUserCanNotRegisterWithWrongEmailDomain(): void
    {
        $response = $this->postJson("/api/auth/register", $this->validData(["email" => "user@nonexistentdomain.xyzabc"]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors("email");
    }

    public function testUserCanNotRegisterWithTooLongName(): void
    {
        $longName = str_repeat("a", 256);
        $response = $this->postJson("/api/auth/register", $this->validData(["firstname" => $longName]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors("firstname");
    }

    public function testUserCanNotRegisterWithTooLongSurname(): void
    {
        $longSurname = str_repeat("b", 256);
        $response = $this->postJson("/api/auth/register", $this->validData(["surname" => $longSurname]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors("surname");
    }

    public function testUserCanNotRegisterWithTooLongPassword(): void
    {
        $longPassword = str_repeat("p", 256);
        $response = $this->postJson("/api/auth/register", $this->validData(["password" => $longPassword]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors("password");
    }

    private function validData(array $overrides = []): array
    {
        return array_merge([
            "firstname" => "Jan",
            "surname" => "Kowalski",
            "email" => "jan.kowalski@gmail.com",
            "password" => "securePassword123",
        ], $overrides);
    }
}
