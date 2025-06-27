<?php

declare(strict_types=1);

namespace Interns2025b\Actions;

use Illuminate\Support\Facades\Hash;
use Interns2025b\Models\User;

class RegisterUserAction
{
    public function execute(array $data): ?User
    {
        if (User::query()->where("email", $data["email"])->exists()) {
            return null;
        }
        $user = new User($data);
        $user->password = Hash::make($data["password"]);
        $user->save();
        $user->assignRole("user");
        $user->sendEmailVerificationNotification();

        return $user;
    }
}
