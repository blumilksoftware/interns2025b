<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Interns2025b\Actions\RegisterUserAction;
use Interns2025b\Http\Requests\StoreUserRequest;
use Interns2025b\Http\Requests\UpdateUserRequest;
use Interns2025b\Http\Resources\UserResource;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;

class UserManagementController extends Controller
{
    public function index(User $user): JsonResponse
    {
        $users = User::query()
            ->with("organizations")
            ->role("user")
            ->orderBy("id")
            ->get();

        return response()->json(UserResource::collection($users), Status::HTTP_OK);
    }

    public function show(User $user): JsonResponse
    {
        if (!$user->hasRole("user")) {
            abort(Status::HTTP_FORBIDDEN, __("users.only_user_role_manage"));
        }

        $user->load("organizations");

        return response()->json(new UserResource($user), Status::HTTP_OK);
    }

    public function store(StoreUserRequest $request, RegisterUserAction $registerUser): JsonResponse
    {
        $data = $request->validated();

        $user = $registerUser->execute($data);

        if (!$user) {
            return response()->json([
                "message" => __("users.email_exists"),
            ], Status::HTTP_CONFLICT);
        }

        if (isset($data["organization_ids"])) {
            $user->organizations()->sync($data["organization_ids"]);
        }

        return response()->json(new UserResource($user), Status::HTTP_CREATED);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        if (!$user->hasRole("user")) {
            abort(Status::HTTP_FORBIDDEN, __("users.only_user_role_manage"));
        }

        $data = $request->validated();

        $emailChanged = isset($data["email"]) && $data["email"] !== $user->email;

        if (isset($data["password"])) {
            $data["password"] = Hash::make($data["password"]);
        }

        $user->update($data);

        if (isset($data["organization_ids"])) {
            $user->organizations()->sync($data["organization_ids"]);
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
        if (!$user->hasRole("user")) {
            abort(Status::HTTP_FORBIDDEN, __("users.only_user_role_manage"));
        }

        $user->delete();

        return response()->json(["message" => __("users.deleted_successfully")], Status::HTTP_OK);
    }
}
