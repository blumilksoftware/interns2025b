<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Interns2025b\Http\Resources\EventResource;
use Interns2025b\Models\Event;

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
        $event->loadOwnerRelations();

        return response()->json([
            "data" => new EventResource($event),
        ]);
    }
}
