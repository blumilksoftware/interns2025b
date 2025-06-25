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
        $validated = $request->validated();

        if (Hash::check($validated["new_password"], $user->password)) {
            return response()->json([
                "message" => "New password cannot be the same as the current password.",
            ], Status::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->password = Hash::make($validated["new_password"]);
        $user->save();

        activity()
            ->performedOn($user)
            ->log("Changed password via profile");

        return response()->json([
            "message" => "Password updated successfully.",
        ], Status::HTTP_OK);
    }
}
