<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Interns2025b\Http\Requests\StoreOrganizationRequest;
use Interns2025b\Http\Requests\UpdateOrganizationRequest;
use Interns2025b\Http\Resources\OrganizationResource;
use Interns2025b\Models\Organization;
use Symfony\Component\HttpFoundation\Response as Status;

class OrganizationManagementController extends Controller
{
    public function index(): JsonResponse
    {
        $organizations = Organization::orderBy("id")->get();

        return response()->json(OrganizationResource::collection($organizations), Status::HTTP_OK);
    }

    public function show(Organization $organization): JsonResponse
    {
        return response()->json(new OrganizationResource($organization), Status::HTTP_OK);
    }

    public function store(StoreOrganizationRequest $request): JsonResponse
    {
        $organization = Organization::create($request->validated());

        return response()->json(new OrganizationResource($organization), Status::HTTP_CREATED);
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization): JsonResponse
    {
        $organization->update($request->validated());

        return response()->json(new OrganizationResource($organization), Status::HTTP_OK);
    }

    public function destroy(Organization $organization): JsonResponse
    {
        $organization->delete();

        return response()->json(["message" => "Organization deleted successfully."], Status::HTTP_OK);
    }
}
