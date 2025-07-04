<?php

declare(strict_types=1);

namespace Interns2025b\Actions;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Interns2025b\Models\User;

class ResetPasswordAction
{
    public function execute(array $credentials): string
    {
        $status = Password::reset(
            $credentials,
            function (User $user, string $password): void {
                $user->forceFill([
                    "password" => Hash::make($password),
                    "remember_token" => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                activity()
                    ->performedOn($user)
                    ->log("Reset password via API");
            },
        );

        return $status;
    }
}
