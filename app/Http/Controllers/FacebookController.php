<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Interns2025b\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class FacebookController extends Controller
{
    public function redirect(): JsonResponse
    {
        $url = Socialite::driver("facebook")->redirect()->getTargetUrl();

        return response()->json(["url" => $url]);
    }

    public function loginCallback(): JsonResponse
    {
        $facebookUser = Socialite::driver("facebook")->user();
        $email = $facebookUser->getEmail();

        $existingUser = User::where("email", $email)->first();

        if ($existingUser && $existingUser->facebook_id === null) {
            return response()->json([
                "message" => "Email already registered. Please log in with your password first.",
            ], Response::HTTP_FORBIDDEN);
        }

        $nameParts = explode(" ", $facebookUser->getName(), 2);
        $firstName = $nameParts[0] ?? "Facebook";

        $user = User::firstOrCreate(
            ["facebook_id" => $facebookUser->getId()],
            [
                "first_name" => $firstName,
                "email" => $email,
                "password" => null,
            ],
        );

        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->assignRole("user");
            $user->save();
        }

        $token = $user->createToken("token")->plainTextToken;

        return response()->json([
            "message" => "success",
            "token" => $token,
            "user_id" => $user->id,
        ], Response::HTTP_OK);
    }

    public function linkCallback(): JsonResponse
    {
        $facebookUser = Socialite::driver("facebook")->user();
        $user = Auth::user();

        if (User::where("facebook_id", $facebookUser->getId())->where("id", "!=", $user->id)->exists()) {
            return response()->json([
                "message" => "Facebook account already linked to another user",
            ], Response::HTTP_CONFLICT);
        }

        $user->facebook_id = $facebookUser->getId();

        $user->save();

        return response()->json([
            "message" => "Facebook account linked successfully",
        ], Response::HTTP_OK);
    }
}
