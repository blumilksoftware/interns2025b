<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Interns2025b\Http\Resources\OrganizationResource;
use Interns2025b\Models\Organization;

class OrganizationController extends Controller
{
    public function index(): JsonResponse
    {
        $organizations = Organization::query()->with(["owner", "users"])->get();

        return response()->json([
            "data" => OrganizationResource::collection($organizations),
        ]);
    }

    public function show(Organization $organization): JsonResponse
    {
        return response()->json([
            "data" => new OrganizationResource($organization),
        ]);
    }
}
