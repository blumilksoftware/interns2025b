<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Interns2025b\Actions\ToggleFollowAction;
use Interns2025b\Http\Resources\EventShortResource;
use Interns2025b\Http\Resources\OrganizationShortResource;
use Interns2025b\Http\Resources\UserShortResource;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response as Status;

class FollowController
{
    public function __invoke(Request $request, string $type, int $id, ToggleFollowAction $action): JsonResponse
    {
        try {
            $message = $action->execute($request->user(), $type, $id);

            return response()->json([
                "message" => $message,
            ], Status::HTTP_OK);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                "message" => $e->getMessage(),
            ], Status::HTTP_BAD_REQUEST);
        }
    }

    public function followings(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load("followingUsers", "followingOrganizations", "followingEvents");

        return response()->json([
            "users" => UserShortResource::collection($user->followingUsers),
            "organizations" => OrganizationShortResource::collection($user->followingOrganizations),
            "events" => EventShortResource::collection($user->followingEvents),
        ], Status::HTTP_OK);
    }

    public function followers(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            "followers" => UserShortResource::collection($user->followers),
        ], Status::HTTP_OK);
    }
}
