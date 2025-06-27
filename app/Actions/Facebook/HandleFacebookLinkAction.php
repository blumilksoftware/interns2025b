<?php

declare(strict_types=1);

namespace Interns2025b\Actions\Facebook;

use Illuminate\Support\Facades\Auth;
use Interns2025b\Models\User;
use Laravel\Socialite\Contracts\User as FacebookUser;

class HandleFacebookLinkAction
{
    public function execute(FacebookUser $facebookUser): array
    {
        $user = Auth::user();

        $facebookId = $facebookUser->getId();
        $email = $facebookUser->getEmail();

        if (!$facebookId) {
            return ["error" => "auth.facebook_id_required", "status" => 422];
        }

        if (User::query()
            ->where("facebook_id", $facebookId)
            ->where("id", "!=", $user->id)
            ->exists()) {
            return ["error" => "auth.facebook_account_already_linked", "status" => 409];
        }

        if (!$email) {
            return ["error" => "auth.email_required_from_facebook", "status" => 422];
        }

        $existingUser = User::query()
            ->where("email", $email)
            ->where("id", "!=", $user->id)
            ->first();

        if ($existingUser && $existingUser->facebook_id === null) {
            return ["error" => "auth.email_already_registered", "status" => 403];
        }

        $user->facebook_id = $facebookId;
        $user->save();

        return ["message" => "auth.facebook_link_success"];
    }
}
