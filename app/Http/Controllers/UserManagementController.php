<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Interns2025b\Http\Requests\StoreUserRequest;
use Interns2025b\Http\Requests\UpdateUserRequest;
use Interns2025b\Http\Resources\UserResource;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;

class UserManagementController extends Controller
{
    public function index(User $user): JsonResponse
    {
        $users = User::with("organizations")
            ->role("user")
            ->orderBy("id")
            ->get();

        return response()->json(UserResource::collection($users), Status::HTTP_OK);
    }

    public function show(User $user): JsonResponse
    {
        if (!$user->hasRole("user")) {
            abort(Status::HTTP_FORBIDDEN, 'Only users with the "user" role can be managed here.');
        }

        $user->load("organizations");

        return response()->json(new UserResource($user), Status::HTTP_OK);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $userExists = User::query()->where("email", $data["email"])->exists();

        if ($userExists) {
            return response()->json([
                "message" => "User with this email already exists.",
            ], Status::HTTP_CONFLICT);
        }

        $user = new User($data);
        $user->password = Hash::make($data["password"]);
        $user->save();

        $user->assignRole("user");

        if (isset($data["organization_ids"])) {
            $user->organizations()->sync($data["organization_ids"]);
        }

        if (method_exists($user, "sendEmailVerificationNotification")) {
            $user->sendEmailVerificationNotification();
        }

        return response()->json(new UserResource($user), Status::HTTP_CREATED);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        if (!$user->hasRole("user")) {
            abort(Status::HTTP_FORBIDDEN, 'Only users with the "user" role can be managed here.');
        }

        $data = $request->validated();

        $emailChanged = isset($data["email"]) && $data["email"] !== $user->email;

        if ($emailChanged) {
            $data["email_verified_at"] = null;
        }

        if (isset($data["password"])) {
            $data["password"] = Hash::make($data["password"]);
        }

        $user->update($data);

        if (isset($data["organization_ids"])) {
            $user->organizations()->sync($data["organization_ids"]);
        }

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        return response()->json(new UserResource($user), Status::HTTP_OK);
    }

    public function destroy(User $user): JsonResponse
    {
        if (!$user->hasRole("user")) {
            abort(Status::HTTP_FORBIDDEN, 'Only users with the "user" role can be managed here.');
        }

        $user->delete();

        return response()->json(["message" => "User deleted successfully."], Status::HTTP_OK);
    }
}
