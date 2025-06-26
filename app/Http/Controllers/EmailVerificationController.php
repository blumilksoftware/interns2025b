<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;

class EmailVerificationController extends Controller
{
    public function verify(Request $request, int $id, string $hash): JsonResponse
    {
        $user = User::query()->find($id);

        if (!$user) {
            return response()->json([
                "message" => __("auth.user_not_found"),
            ], Status::HTTP_NOT_FOUND);
        }

        if (!URL::hasValidSignature($request)) {
            return response()->json([
                "message" => __("auth.invalid_or_expired_link"),
            ], Status::HTTP_FORBIDDEN);
        }

        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                "message" => __("auth.invalid_verification_hash"),
            ], Status::HTTP_FORBIDDEN);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                "message" => __("auth.email_already_verified"),
            ], Status::HTTP_OK);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json([
            "message" => __("auth.email_verified_successfully"),
        ], Status::HTTP_OK);
    }
}
