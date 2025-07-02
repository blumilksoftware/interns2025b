<?php

declare(strict_types=1);

namespace Interns2025b\Actions\Facebook;

use Interns2025b\Models\User;
use Laravel\Socialite\Contracts\User as FacebookUser;

class HandleFacebookLoginAction
{
    public function execute(FacebookUser $facebookUser): array
    {
        $facebookId = $facebookUser->getId();
        $email = $facebookUser->getEmail();
        $name = trim((string)$facebookUser->getName());

        if (!$facebookId) {
            return ["error" => "auth.facebook_id_required", "status" => 422];
        }

        if (empty($name)) {
            return ["error" => "auth.facebook_name_required", "status" => 422];
        }

        $user = User::query()->where("facebook_id", $facebookId)->first();

        if ($user) {
            return [
                "message" => "success",
                "token" => $user->createToken("token")->plainTextToken,
                "user_id" => $user->id,
            ];
        }

        if (!$email) {
            return ["error" => "auth.email_required_from_facebook", "status" => 422];
        }

        if (User::query()->where("email", $email)->exists()) {
            return ["error" => "auth.email_already_registered", "status" => 403];
        }

        $firstName = explode(" ", $name, 2)[0];

        $newUser = User::query()->create([
            "first_name" => $firstName,
            "email" => $email,
            "facebook_id" => $facebookId,
            "password" => null,
            "email_verified_at" => now(),
        ]);

        return [
            "message" => "success",
            "token" => $newUser->createToken("token")->plainTextToken,
            "user_id" => $newUser->id,
        ];
    }
}
