<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Interns2025b\Models\Organization;

class OrganizationController extends Controller
{
    public function index(): JsonResponse
    {
        $organizations = Organization::query()->with(["owner", "users"])->get();

        return response()->json([
            "data" => $organizations,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $organization = Organization::query()->with(["owner", "users"])->findOrFail($id);

        return response()->json([
            "data" => $organization,
        ]);
    }
}
