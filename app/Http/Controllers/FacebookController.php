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
        $facebookUser = Socialite::driver("facebook")->stateless()->user();

        $nameParts = explode(" ", $facebookUser->getName(), 2);
        $firstname = $nameParts[0] ?? "Facebook";

        $user = User::firstOrCreate(
            ["facebook_id" => $facebookUser->getId()],
            [
                "firstname" => $firstname,
                "surname" => "",
                "email" => $facebookUser->getEmail(),
                "avatar" => $facebookUser->getAvatar(),
                "password" => null,
            ],
        );

        if (!$user->avatar) {
            $user->avatar = $facebookUser->getAvatar();
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
        $facebookUser = Socialite::driver("facebook")->stateless()->user();
        $user = Auth::user();

        if (User::where("facebook_id", $facebookUser->getId())->where("id", "!=", $user->id)->exists()) {
            return response()->json([
                "message" => "Facebook account already linked to another user",
            ], Response::HTTP_CONFLICT);
        }

        $user->facebook_id = $facebookUser->getId();

        if (!$user->avatar) {
            $user->avatar = $facebookUser->getAvatar();
        }

        $user->save();

        return response()->json([
            "message" => "Facebook account linked successfully",
        ], Response::HTTP_OK);
    }
}
