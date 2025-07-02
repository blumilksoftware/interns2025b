<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Interns2025b\Http\Resources\EventResource;
use Interns2025b\Models\Event;
use Symfony\Component\HttpFoundation\Response as Status;

class EventController extends Controller
{
    public function index(): JsonResponse
    {
        $events = Event::loadWithOwnerRelations();

        return response()->json([
            "data" => EventResource::collection($events)]);
    }

    public function show(Event $event): JsonResponse
    {
        $event->load("owner");

        return response()->json([
            "data" => new EventResource($event),
        ], Status::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            "title" => "required|string|max:255",
            "description" => "nullable|string",
            "start" => "required|date",
            "end" => "required|date|after_or_equal:start",
            "owner_type" => "required|string",
            "owner_id" => "required|integer",
        ]);

        $event = Event::create($data);

        return response()->json($event, Status::HTTP_CREATED);
    }

    public function update(Request $request, Event $event): JsonResponse
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
        ], Status::HTTP_OK);
    }

    public function destroy(Event $event): JsonResponse
    {
        $event->delete();

        return response()->json([
            "message" => "Event deleted successfully.",
        ], Status::HTTP_OK);
    }
}
