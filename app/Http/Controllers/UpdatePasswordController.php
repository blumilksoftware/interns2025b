<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Interns2025b\Http\Requests\UpdatePasswordRequest;
use Symfony\Component\HttpFoundation\Response as Status;

class UpdatePasswordController extends Controller
{
    private const REQUEST_COOLDOWN = 900;

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = Auth::user();
        $limiter = app(RateLimiter::class);
        $key = "password-update:" . $user->id;

        if ($limiter->tooManyAttempts($key, 1)) {
            return response()->json([
                "message" => __("passwords.throttled"),
            ], Status::HTTP_TOO_MANY_REQUESTS);
        }

        $limiter->hit($key, self::REQUEST_COOLDOWN);

        $newPassword = $request->getNewPassword();

        if (Hash::check($newPassword, $user->password)) {
            return response()->json([
                "message" => __("passwords.same_as_current"),
            ], Status::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        activity()
            ->performedOn($user)
            ->log("Changed password via profile");

        return response()->json([
            "message" => __("passwords.updated_successfully"),
        ], Status::HTTP_OK);
    }
}
