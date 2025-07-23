<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Interns2025b\Mail\DeleteAccountLinkMail;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;

class UserDeletionController extends Controller
{
    public function requestDelete(Request $request): JsonResponse
    {
        $user = $request->user();
        $limiter = app(RateLimiter::class);

        $key = "delete-request:" . $user->id;

        if ($limiter->tooManyAttempts($key, 1)) {
            return response()->json([
                "status" => "error",
                "message" => __("profile.throttled"),
            ], Status::HTTP_TOO_MANY_REQUESTS);
        }

        $limiter->hit($key, 900);

        $url = URL::temporarySignedRoute(
            "api.confirmDelete",
            now()->addMinutes(60),
            ["user" => $user->id],
        );

        Mail::to($user->email)->send(new DeleteAccountLinkMail($user, $url));

        return response()->json([
            "status" => "success",
            "message" => __("profile.email_sent"),
        ], Status::HTTP_OK);
    }

    public function confirmDelete(Request $request, $user): JsonResponse
    {
        $user = User::findOrFail($user);

        $user->delete();

        return response()->json([
            "status" => "success",
            "message" => __("profile.deleted"),
        ], Status::HTTP_OK);
    }
}
