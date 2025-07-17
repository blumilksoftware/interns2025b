<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Interns2025b\Http\Requests\StoreEventRequest;
use Interns2025b\Http\Requests\UpdateEventRequest;
use Interns2025b\Http\Resources\EventResource;
use Interns2025b\Models\Event;
use Symfony\Component\HttpFoundation\Response as Status;

class EventController extends Controller
{
    public function index(): JsonResponse
    {
        $perPage = request()->integer("per_page", 10);
        $paginated = Event::with("owner")->paginate($perPage);

        return EventResource::collection($paginated)->response();
    }

    public function show(Event $event): JsonResponse
    {
        $event->load("owner");

        return response()->json([
            "data" => new EventResource($event),
        ]);
    }

    public function store(StoreEventRequest $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validated();
        $data["owner_type"] = get_class($user);
        $data["owner_id"] = $user->id;

        $event = Event::create($data);

        return response()->json([
            "data" => new EventResource($event),
        ], Status::HTTP_CREATED);
    }

    public function update(UpdateEventRequest $request, Event $event): JsonResponse
    {
        $this->authorize("update", $event);

        $data = $request->validated();

        $event->update($data);
        $event->loadOwnerRelations();

        return response()->json([
            "data" => new EventResource($event),
        ]);
    }

    public function destroy(Event $event): JsonResponse
    {
        $this->authorize("delete", $event);

        $event->delete();

        return response()->json([
            "message" => "Event deleted successfully.",
        ]);
    }
}
