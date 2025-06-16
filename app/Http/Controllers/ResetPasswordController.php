<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Interns2025b\Http\Requests\PasswordResetLinkRequest;
use Interns2025b\Http\Requests\ResetPasswordRequest;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;

class ResetPasswordController extends Controller
{
    public function sendResetLinkEmail(PasswordResetLinkRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only("email"),
        );

        activity()
            ->withProperties(["email" => $request->input("email")])
            ->log("Requested password reset link via API");

        return $status === Password::RESET_LINK_SENT
            ? response()->json(["message" => __("passwords.sent")], Status::HTTP_OK)
            : response()->json(["message" => __("passwords.user")], Status::HTTP_BAD_REQUEST);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only("email", "password", "password_confirmation", "token"),
            function (User $user, string $password): void {
                $user->forceFill([
                    "password" => Hash::make($password),
                    "remember_token" => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                activity()
                    ->performedOn($user)
                    ->log("Reset password via API");
            },
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(["message" => __("passwords.reset")], Status::HTTP_OK)
            : response()->json(["message" => __($status)], Status::HTTP_BAD_REQUEST);
    }
}
