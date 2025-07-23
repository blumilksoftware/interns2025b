<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Interns2025b\Http\Requests\StoreEventRequest;
use Interns2025b\Http\Requests\UpdateEventRequest;
use Interns2025b\Http\Resources\EventResource;
use Interns2025b\Models\Event;
use Interns2025b\Models\Organization;
use Symfony\Component\HttpFoundation\Response as Status;

class OrganizationEventController extends Controller
{
    public function index(Organization $organization): JsonResponse
    {
        $events = $organization->ownedEvents()->with("owner")->get();

        return response()->json([
            "data" => EventResource::collection($events),
        ]);
    }

    public function store(StoreEventRequest $request, Organization $organization): JsonResponse
    {
        $this->authorize("create", [Event::class, $organization]);

        $event = $organization->ownedEvents()->create($request->validated() + [
            "owner_type" => Organization::class,
            "owner_id" => $organization->id,
        ]);

        return response()->json([
            "message" => __("events.created"),
            "data" => new EventResource($event),
        ], Status::HTTP_CREATED);
    }

    public function update(UpdateEventRequest $request, Organization $organization, Event $event): JsonResponse
    {
        $this->authorize("update", $event);

        $event->update($request->validated());

        $event->loadOwnerRelations();

        return response()->json([
            "message" => __("events.updated"),
            "data" => new EventResource($event),
        ]);
    }

    public function destroy(Organization $organization, Event $event): JsonResponse
    {
        $this->authorize("delete", $event);

        $event->delete();

        return response()->json([
            "message" => __("events.deleted_from_organization"),
        ]);
    }
}
