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
        $events = Event::query()->with(["owner"])->latest()->get();

        return response()->json([
            "data" => EventResource::collection($events)]);
    }

    public function show(Event $event): JsonResponse
    {
        return response()->json([
            "data" => new EventResource($event),
        ]);
    }
}
