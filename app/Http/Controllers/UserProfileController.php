<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Interns2025b\Http\Requests\UpdateUserRequest;
use Interns2025b\Http\Resources\UserDetailResource;
use Interns2025b\Http\Resources\UserResource;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;

class UserProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->loadCount([
            "ownedEvents",
            "followers",
            "followingUsers",
        ])->load(["organizations"]);

        return response()->json([
            "message" => __("profile.retrieved"),
            "data" => new UserResource($user),
        ])->setStatusCode(Status::HTTP_OK);
    }

    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        $user->loadCount([
            "ownedEvents",
            "followers",
            "followingUsers",
        ])->load([
            "organizations",
        ]);

        return response()->json([
            "message" => __("profile.updated"),
            "data" => new UserResource($user),
        ])->setStatusCode(Status::HTTP_OK);
    }

    public function showDetail(Request $request, User $user): JsonResponse
    {
        if ($request->user()->id === $user->id) {
            return response()->json([
                "message" => __("profile.redirected"),
                "redirect" => "/api/profile",
            ], 302);
        }

        $user->load(["ownedEvents"])->loadCount(["ownedEvents", "followers", "followingUsers"]);

        return response()->json([
            "message" => __("profile.retrieved"),
            "data" => new UserDetailResource($user),
        ])->setStatusCode(Status::HTTP_OK);
    }
}
