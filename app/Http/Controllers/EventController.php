<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Interns2025b\Models\Event;

class EventController extends Controller
{
    public function index(): JsonResponse
    {
        $events = Event::with(["owner"])->latest()->get();

        return response()->json([
            "data" => $events]);
    }

    public function show(int $id): JsonResponse
    {
        $event = Event::with(["owner"])->findOrFail($id);

        return response()->json([
            "data" => $event,
        ]);
    }
}
