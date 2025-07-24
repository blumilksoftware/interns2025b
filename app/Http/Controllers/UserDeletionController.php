<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Interns2025b\Actions\ThrottleAction;
use Interns2025b\Mail\DeleteAccountLinkMail;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;

class UserDeletionController extends Controller
{
    public function requestDelete(Request $request, ThrottleAction $throttle): JsonResponse
    {
        $user = $request->user();
        $key = "delete-request:" . $user->id;

        $throttle->handle($key, "15min", "profile.throttled");

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
