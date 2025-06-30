<?php

declare(strict_types=1);

namespace Interns2025b\Actions;

use Illuminate\Support\Facades\Auth;
use Interns2025b\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class LogoutUserAction
{
    public function execute(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        } else {
            Auth::guard("web")->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }
    }
}
