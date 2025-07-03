<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Interns2025b\Http\Requests\StoreAdminRequest;
use Interns2025b\Http\Requests\UpdateAdminRequest;
use Interns2025b\Http\Resources\AdminResource;
use Interns2025b\Models\User;
use Symfony\Component\HttpFoundation\Response as Status;

class AdminManagementController extends Controller
{
    public function index(): JsonResponse
    {
        $admins = User::role(["administrator"])
            ->orderBy("id")
            ->get();

        return response()->json(AdminResource::collection($admins), Status::HTTP_OK);
    }

    public function show(User $admin): JsonResponse
    {
        if (!$admin->hasAnyRole(["administrator"])) {
            abort(Status::HTTP_FORBIDDEN, __("users.forbidden_role"));
        }

        return response()->json(new AdminResource($admin), Status::HTTP_OK);
    }

    public function store(StoreAdminRequest $request): JsonResponse
    {
        $data = $request->validated();

        $admin = new User($data);
        $admin->password = Hash::make($data["password"]);
        $admin->save();

        $admin->assignRole("administrator");

        if (method_exists($admin, "sendEmailVerificationNotification")) {
            $admin->sendEmailVerificationNotification();
        }

        return response()->json(new AdminResource($admin), Status::HTTP_CREATED);
    }

    public function update(UpdateAdminRequest $request, User $admin): JsonResponse
    {
        if (!$admin->hasRole("administrator")) {
            abort(Status::HTTP_FORBIDDEN, __("users.forbidden_role"));
        }

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
        if (!$admin->hasAnyRole(["administrator"])) {
            abort(Status::HTTP_FORBIDDEN, __("users.forbidden_role"));
        }

        $admin->delete();

        return response()->json(["message" => __("users.admin_deleted")], Status::HTTP_OK);
    }
}
