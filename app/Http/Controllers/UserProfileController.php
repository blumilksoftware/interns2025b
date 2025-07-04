<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Interns2025b\Http\Requests\UpdateUserRequest;
use Interns2025b\Http\Resources\UserDetailResource;
use Interns2025b\Http\Resources\UserResource;
use Symfony\Component\HttpFoundation\Response as Status;

class UserProfileController extends Controller
{
    public function show(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            "message" => __("profile.retrieved"),
            "data" => new UserResource($user),
        ])->setStatusCode(Status::HTTP_OK);
    }

    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = Auth::user();
        $user->update($request->validated());

        return response()->json([
            "message" => __("profile.updated"),
            "data" => new UserResource($user),
        ])->setStatusCode(Status::HTTP_OK);
    }

    public function showDetail(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->route("profile.me");
        }

        return response()->json([
            "message" => __("profile.retrieved"),
            "data" => new UserDetailResource($user),
        ]);
    }

    public function me()
    {
        return response()->json([
            "message" => __("profile.retrieved"),
            "data" => new UserDetailResource(Auth::user()),
        ]);
    }
}
