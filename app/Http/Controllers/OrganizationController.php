<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Interns2025b\Http\Requests\StoreOrganizationRequest;
use Interns2025b\Http\Requests\UpdateOrganizationRequest;
use Interns2025b\Http\Resources\OrganizationResource;
use Interns2025b\Models\Organization;
use Symfony\Component\HttpFoundation\Response as Status;

class OrganizationController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize("viewAny", Organization::class);

        $organizations = Organization::query()->with(["owner", "users"])->get();

        return response()->json([
            "data" => OrganizationResource::collection($organizations),
        ]);
    }

    public function show(Organization $organization): JsonResponse
    {
        $this->authorize("view", $organization);

        return response()->json([
            "data" => new OrganizationResource($organization),
        ]);
    }

    public function store(StoreOrganizationRequest $request): JsonResponse
    {
        $this->authorize("create", Organization::class);

        $organization = Organization::create($request->validated());

        return response()->json([
            "message" => __("organization.created_successfully"),
            "data" => new OrganizationResource($organization),
        ], Status::HTTP_CREATED);
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization): JsonResponse
    {
        $this->authorize("update", $organization);

        $organization->update($request->validated());

        return response()->json([
            "message" => __("organization.updated_successfully"),
            "data" => new OrganizationResource($organization),
        ], Status::HTTP_OK);
    }

    public function destroy(Organization $organization): JsonResponse
    {
        $this->authorize("delete", $organization);

        $organization->delete();

        return response()->json([
            "message" => __("organization.deleted_successfully"),
        ], Status::HTTP_OK);
    }
}
