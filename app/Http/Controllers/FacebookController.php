<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Interns2025b\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
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
        try {
            $facebookUser = Socialite::driver("facebook")->stateless()->user();
        } catch (InvalidStateException | Exception $e) {
            return response()->json([
                "message" => __("auth.facebook_error"),
                "error" => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $facebookId = $facebookUser->getId();
        $email = $facebookUser->getEmail();
        $name = trim((string)$facebookUser->getName());

        if (!$facebookId) {
            return response()->json([
                "message" => __("auth.facebook_id_required"),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (empty($name)) {
            return response()->json([
                "message" => __("auth.facebook_name_required"),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::query()->where("facebook_id", $facebookId)->first();

        if ($user) {
            $token = $user->createToken("token")->plainTextToken;

            return response()->json([
                "message" => "success",
                "token" => $token,
                "user_id" => $user->id,
            ], Response::HTTP_OK);
        }

        if (!$email) {
            return response()->json([
                "message" => __("auth.email_required_from_facebook"),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $existingUser = User::query()->where("email", $email)->first();

        if ($existingUser) {
            return response()->json([
                "message" => __("auth.email_already_registered"),
            ], Response::HTTP_FORBIDDEN);
        }

        $firstName = explode(" ", $name, 2)[0];

        $newUser = User::create([
            "first_name" => $firstName,
            "email" => $email,
            "facebook_id" => $facebookId,
            "password" => null,
            "email_verified_at" => now(),
        ]);

        $token = $newUser->createToken("token")->plainTextToken;

        return response()->json([
            "message" => "success",
            "token" => $token,
            "user_id" => $newUser->id,
        ], Response::HTTP_OK);
    }

    public function linkCallback(): JsonResponse
    {
        try {
            $facebookUser = Socialite::driver("facebook")->stateless()->user();
        } catch (InvalidStateException | Exception $e) {
            return response()->json([
                "message" => __("auth.facebook_error"),
                "error" => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = Auth::user();

        $facebookId = $facebookUser->getId();
        $email = $facebookUser->getEmail();

        if (!$facebookId) {
            return response()->json([
                "message" => __("auth.facebook_id_required"),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (User::where("facebook_id", $facebookId)
            ->where("id", "!=", $user->id)
            ->exists()) {
            return response()->json([
                "message" => __("auth.facebook_account_already_linked"),
            ], Response::HTTP_CONFLICT);
        }

        if (!$email) {
            return response()->json([
                "message" => __("auth.email_required_from_facebook"),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $existingUser = User::where("email", $email)
            ->where("id", "!=", $user->id)
            ->first();

        if ($existingUser && $existingUser->facebook_id === null) {
            return response()->json([
                "message" => __("auth.email_already_registered"),
            ], Response::HTTP_FORBIDDEN);
        }

        $user->facebook_id = $facebookId;
        $user->save();

        return response()->json([
            "message" => __("auth.facebook_link_success"),
        ], Response::HTTP_OK);
    }
}
