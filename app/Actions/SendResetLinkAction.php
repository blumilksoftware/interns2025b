<?php

declare(strict_types=1);

namespace Interns2025b\Actions;

use Illuminate\Support\Facades\Password;

class SendResetLinkAction
{
    public function execute(array $credentials): string
    {
        Password::sendResetLink($credentials);

        activity()
            ->withProperties(["email" => $credentials->input["email"] ?? null])
            ->log("Requested password reset link via API");

        return __("passwords.sent");
    }
}
