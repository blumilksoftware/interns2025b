<?php

declare(strict_types=1);

namespace Interns2025b\Actions;

use Illuminate\Support\Facades\Hash;
use Interns2025b\DTO\RegisterUserDto;
use Interns2025b\Enums\Role;
use Interns2025b\Models\User;

class RegisterUserAction
{
    public function execute(RegisterUserDto $dto): ?User
    {
        if (User::query()->where("email", $dto->email)->exists()) {
            return null;
        }

        $user = User::create([
            "first_name" => $dto->firstName,
            "last_name" => $dto->lastName,
            "email" => $dto->email,
            "password" => Hash::make($dto->password),
        ]);

        $user->assignRole(Role::User->value);
        $user->sendEmailVerificationNotification();

        return $user;
    }
}
