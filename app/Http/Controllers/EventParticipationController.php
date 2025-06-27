<?php

declare(strict_types=1);

namespace Interns2025b\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Interns2025b\Models\Event;
use Symfony\Component\HttpFoundation\Response as Status;

class EventParticipationController
{
    public function __invoke(Request $request, Event $event): JsonResponse
    {
        $user = $request->user();

        if (in_array($event->status, ["ended", "canceled"], true)) {
            return response()->json([
                "message" => __("events.cannot_join"),
            ], Status::HTTP_FORBIDDEN);
        }

        $isParticipating = $event->participants()->where("user_id", $user->id)->exists();

        if ($isParticipating) {
            $event->participants()->detach($user->id);

            return response()->json([
                "message" => __("events.leave_success"),
            ]);
        }

        $event->participants()->attach($user->id);

        return response()->json([
            "message" => __("events.join_success"),
        ]);
    }
}
