<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Interns2025b\Http\Resources\EventResource;
use Interns2025b\Models\Event;
use Interns2025b\Models\Organization;
use Symfony\Component\HttpFoundation\Response as Status;

class OrganizationEventController extends Controller
{
    public function index(Organization $organization): JsonResponse
    {
        $events = $organization->events()->with("owner")->get();

        return response()->json([
            "data" => EventResource::collection($events),
        ]);
    }

    public function store(Request $request, Organization $organization): JsonResponse
    {
        $data = $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "start" => "required|date",
            "end" => "required|date|after_or_equal:start",
        ]);

        $event = $organization->events()->create(array_merge($data, [
            "owner_type" => get_class($organization),
            "owner_id" => $organization->id,
        ]));

        return response()->json([
            "data" => new EventResource($event),
        ], Status::HTTP_CREATED);
    }

    public function update(Request $request, Organization $organization, Event $event): JsonResponse
    {
        $data = $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "start" => "required|date",
            "end" => "required|date|after_or_equal:start",
        ]);

        $event->update($data);
        $event->loadOwnerRelations();

        return response()->json([
            "data" => new EventResource($event),
        ]);
    }

    public function destroy(Organization $organization, Event $event): JsonResponse
    {
        $event->delete();

        return response()->json([
            "message" => "Event deleted from organization.",
        ]);
    }
}
