<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Interns2025b\Actions\Facebook\HandleFacebookLinkAction;
use Interns2025b\Actions\Facebook\HandleFacebookLoginAction;
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

    public function loginCallback(HandleFacebookLoginAction $action): JsonResponse
    {
        try {
            $facebookUser = Socialite::driver("facebook")->stateless()->user();
        } catch (InvalidStateException | Exception $e) {
            return response()->json([
                "message" => __("auth.facebook_error"),
                "error" => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $result = $action->execute($facebookUser);

        if (isset($result["error"])) {
            return response()->json([
                "message" => __($result["error"]),
            ], $result["status"]);
        }

        return response()->json($result);
    }

    public function linkCallback(HandleFacebookLinkAction $action): JsonResponse
    {
        try {
            $facebookUser = Socialite::driver("facebook")->stateless()->user();
        } catch (InvalidStateException | Exception $e) {
            return response()->json([
                "message" => __("auth.facebook_error"),
                "error" => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $result = $action->execute($facebookUser);

        if (isset($result["error"])) {
            return response()->json([
                "message" => __($result["error"]),
            ], $result["status"]);
        }

        return response()->json([
            "message" => __($result["message"]),
        ]);
    }
}
