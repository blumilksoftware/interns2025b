<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Interns2025b\Http\Requests\UpdatePasswordRequest;
use Symfony\Component\HttpFoundation\Response as Status;

class UpdatePasswordController extends Controller
{
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = Auth::user();
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
