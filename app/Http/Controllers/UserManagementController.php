<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Interns2025b\Actions\RegisterUserAction;
use Interns2025b\Enums\Role;
use Interns2025b\Http\Requests\StoreUserRequest;
use Interns2025b\Http\Requests\UpdateUserRequest;
use Interns2025b\Http\Resources\UserResource;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;

class UserManagementController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize("viewAny", User::class);

        $users = User::query()
            ->with("organizations", "badge")
            ->role(Role::User->value)
            ->orderBy("id")
            ->get();

        return response()->json(UserResource::collection($users), Status::HTTP_OK);
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize("view", $user);

        $user->load("organizations", "badge");

        return response()->json(new UserResource($user), Status::HTTP_OK);
    }

    public function store(StoreUserRequest $request, RegisterUserAction $registerUser): JsonResponse
    {
        $this->authorize("create", User::class);

        $dto = $request->toDto();

        $user = $registerUser->execute($dto);

        if (!$user) {
            return response()->json([
                "message" => __("users.email_exists"),
            ], Status::HTTP_CONFLICT);
        }

        $user->syncRoles(Role::User->value);

        if ($request->filled("organization_ids")) {
            $user->organizations()->sync($request->input("organization_ids"));
        }

        return response()->json(new UserResource($user), Status::HTTP_CREATED);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->authorize("update", $user);

        $dto = $request->toDto();

        $emailChanged = $dto->email !== null && $dto->email !== $user->email;

        $updateData = [
            "first_name" => $dto->firstName ?? $user->first_name,
            "last_name" => $dto->lastName,
            "avatar_url" => $dto->avatarUrl,
            "email" => $dto->email ?? $user->email,
        ];

        $user->update($updateData);

        if ($dto->organizationIds !== null) {
            $user->organizations()->sync($dto->organizationIds);
        }

        if ($emailChanged) {
            $user->email_verified_at = null;
            $user->save();
            $user->sendEmailVerificationNotification();
        }

        return response()->json(new UserResource($user), Status::HTTP_OK);
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize("delete", $user);

        $user->delete();

        return response()->json(["message" => __("users.deleted_successfully")], Status::HTTP_OK);
    }
}
