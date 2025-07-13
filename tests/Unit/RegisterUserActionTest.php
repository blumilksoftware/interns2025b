<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Facades\Hash;
use Interns2025b\Actions\RegisterUserAction;
use Interns2025b\DTO\RegisterUserDto;
use Interns2025b\Models\User;
use Tests\TestCase;

class RegisterUserActionTest extends TestCase
{
    public function testRegistersNewUserAndAssignsRoleAndSendsVerification(): void
    {
        $dto = new RegisterUserDto(
            firstName: "testName",
            lastName: "testLastName",
            email: "test@example.com",
            password: "password123",
        );

        $action = new RegisterUserAction();
        $user = $action->execute($dto);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas("users", [
            "email" => "test@example.com",
            "first_name" => "testName",
            "last_name" => "testLastName",
        ]);
        $this->assertTrue(Hash::check("password123", $user->password));
        $this->assertTrue($user->hasRole("user"));
    }

    public function testReturnsNullIfUserWithEmailAlreadyExists(): void
    {
        User::factory()->create([
            "email" => "existing@example.com",
        ]);

        $dto = new RegisterUserDto(
            firstName: "testName1",
            lastName: "lastname",
            email: "existing@example.com",
            password: "password123",
        );

        $action = new RegisterUserAction();
        $result = $action->execute($dto);

        $this->assertNull($result);
        $this->assertDatabaseMissing("users", [
            "email" => "existing@example.com",
            "first_name" => "testName1",
        ]);
    }
}
