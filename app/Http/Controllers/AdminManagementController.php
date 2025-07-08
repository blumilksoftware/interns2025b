<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Interns2025b\Actions\RegisterUserAction;
use Interns2025b\Enums\Role;
use Interns2025b\Policies\AdminPolicy;
use Interns2025b\Http\Requests\StoreAdminRequest;
use Interns2025b\Http\Requests\UpdateAdminRequest;
use Interns2025b\Http\Resources\AdminResource;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;

class AdminManagementController extends Controller
{
    public function index(): JsonResponse
    {
        $admins = User::query()
            ->role([Role::Administrator->value])
            ->orderBy("id")
            ->get();

        return response()->json(AdminResource::collection($admins), Status::HTTP_OK);
    }

    public function show(User $admin): JsonResponse
    {
        $this->authorize("view", $admin);

        return response()->json(new AdminResource($admin), Status::HTTP_OK);
    }

    public function store(StoreAdminRequest $request, RegisterUserAction $registerUser): JsonResponse
    {
        $this->authorize("create", User::class);

        $data = $request->validated();

        $admin = $registerUser->execute($data);

        if (!$admin) {
            abort(Status::HTTP_UNPROCESSABLE_ENTITY, __("users.duplicate_email"));
        }

        $admin->syncRoles(Role::Administrator->value);

        return response()->json(new AdminResource($admin), Status::HTTP_CREATED);
    }

    public function update(UpdateAdminRequest $request, User $admin): JsonResponse
    {
        $this->authorize("update", $admin);

        $data = $request->validated();

        $emailChanged = isset($data["email"]) && $data["email"] !== $admin->email;

        if (isset($data["password"])) {
            $data["password"] = Hash::make($data["password"]);
        }

        $admin->update($data);

        if ($emailChanged) {
            $admin->email_verified_at = null;
            $admin->save();
            $admin->sendEmailVerificationNotification();
        }

        return response()->json(new AdminResource($admin), Status::HTTP_OK);
    }

    public function destroy(User $admin): JsonResponse
    {
        $this->authorize("delete", $admin);

        $admin->delete();

        return response()->json(["message" => __("users.admin_deleted")], Status::HTTP_OK);
    }
}
