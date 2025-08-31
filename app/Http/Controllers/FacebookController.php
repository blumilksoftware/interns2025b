<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Interns2025b\Actions\Facebook\HandleFacebookLinkAction;
use Interns2025b\Actions\Facebook\HandleFacebookLoginAction;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\Response;

class FacebookController extends Controller
{
    public function redirect(): JsonResponse|RedirectResponse
    {
        $url = Socialite::driver("facebook")->redirect()->getTargetUrl();

        if (request()->wantsJson()) {
            return response()->json(["url" => $url]);
        }

        return Redirect::away($url);
    }

    public function loginCallback(HandleFacebookLoginAction $action): JsonResponse|RedirectResponse
    {
        try {
            $facebookUser = Socialite::driver("facebook")->stateless()->user();
        } catch (InvalidStateException | Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    "message" => __("auth.facebook_error"),
                    "error" => $e->getMessage(),
                ], Response::HTTP_BAD_REQUEST);
            }

            return Redirect::route("home")->with("error", __("auth.facebook_error"));
        }

        $result = $action->execute($facebookUser);

        if (isset($result["error"])) {
            if (request()->wantsJson()) {
                return response()->json([
                    "message" => __($result["error"]),
                ], $result["status"]);
            }

            return Redirect::route("home")->with("error", __($result["error"]));
        }

        $user = $result["user"] ?? null;

        if ($user && !request()->wantsJson()) {
            Auth::login($user, true);
        }

        if (request()->wantsJson()) {
            return response()->json([
                "message" => $result["message"],
                "token" => $result["token"],
                "user_id" => $result["user_id"],
            ]);
        }

        return Redirect::route("home")->with("success", __("auth.facebook_success"));
    }

    public function linkCallback(HandleFacebookLinkAction $action): JsonResponse|RedirectResponse
    {
        try {
            $facebookUser = Socialite::driver("facebook")->stateless()->user();
        } catch (InvalidStateException | Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    "message" => __("auth.facebook_error"),
                    "error" => $e->getMessage(),
                ], Response::HTTP_BAD_REQUEST);
            }

            return Redirect::route("home")->with("error", __("auth.facebook_error"));
        }

        $result = $action->execute($facebookUser);

        if (isset($result["error"])) {
            if (request()->wantsJson()) {
                return response()->json([
                    "message" => __($result["error"]),
                ], $result["status"]);
            }

            return Redirect::route("home")->with("error", __($result["error"]));
        }

        if (request()->wantsJson()) {
            return response()->json([
                "message" => __($result["message"]),
            ]);
        }

        return Redirect::route("home")->with("success", __($result["message"]));
    }
}
