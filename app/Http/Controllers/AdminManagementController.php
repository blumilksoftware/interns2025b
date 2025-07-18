<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Interns2025b\Actions\RegisterUserAction;
use Interns2025b\Enums\Role;
use Interns2025b\Http\Requests\StoreAdminRequest;
use Interns2025b\Http\Requests\UpdateAdminRequest;
use Interns2025b\Http\Resources\AdminResource;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;

class AdminManagementController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize("viewAnyAdmin", User::class);

        $admins = User::query()
            ->role(Role::Administrator->value)
            ->orderBy("id")
            ->get();

        return response()->json(AdminResource::collection($admins), Status::HTTP_OK);
    }

    public function show(User $admin): JsonResponse
    {
        $this->authorize("viewAdmin", $admin);

        return response()->json(new AdminResource($admin), Status::HTTP_OK);
    }

    public function store(StoreAdminRequest $request, RegisterUserAction $registerUser): JsonResponse
    {
        $this->authorize("createAdmin", User::class);

        $dto = $request->toDto();

        $admin = $registerUser->execute($dto);

        if (!$admin) {
            abort(Status::HTTP_UNPROCESSABLE_ENTITY, __("users.duplicate_email"));
        }

        $admin->syncRoles(Role::Administrator->value);

        return response()->json(new AdminResource($admin), Status::HTTP_CREATED);
    }

    public function update(UpdateAdminRequest $request, User $admin): JsonResponse
    {
        $this->authorize("updateAdmin", $admin);

        $dto = $request->toDto();

        $emailChanged = $dto->email !== null && $dto->email !== $admin->email;

        $updateData = [
            "first_name" => $dto->firstName ?? $admin->first_name,
            "last_name" => $dto->lastName,
            "avatar_url" => $dto->avatarUrl ?? $admin->avatar_url,
            "email" => $dto->email ?? $admin->email,
        ];

        $admin->update($updateData);

        if ($emailChanged) {
            $admin->email_verified_at = null;
            $admin->save();
            $admin->sendEmailVerificationNotification();
        }

        return response()->json(new AdminResource($admin), Status::HTTP_OK);
    }

    public function destroy(User $admin): JsonResponse
    {
        $this->authorize("deleteAdmin", $admin);

        $admin->delete();

        return response()->json(["message" => __("users.admin_deleted")], Status::HTTP_OK);
    }
}
