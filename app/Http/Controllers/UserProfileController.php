<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Interns2025b\Http\Requests\UpdateOwnProfileRequest;
use Symfony\Component\HttpFoundation\Response as Status;

class UserProfileController extends Controller
{
    public function show(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            "message" => "User profile retrieved successfully.",
            "data" => [
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "email" => $user->email,
                "facebook_linked" => $user->facebook_id !== null,
            ],
        ])->setStatusCode(Status::HTTP_OK);
    }

    public function update(UpdateOwnProfileRequest $request): JsonResponse
    {
        $user = Auth::user();
        $user->update($request->validated());

        return response()->json([
            "message" => "Profile updated successfully.",
            "data" => [
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "email" => $user->email,
            ],
        ])->setStatusCode(Status::HTTP_OK);
    }
}
