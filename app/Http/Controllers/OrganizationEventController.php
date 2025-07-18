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
        $user = $request->user();

        $belongsToOrganization = $user->organizations()
            ->where("organizations.id", $organization->id)
            ->exists();

        if (!$belongsToOrganization) {
            return response()->json([
                "message" => __("events.not_member"),
            ], Status::HTTP_FORBIDDEN);
        }

        $data = $request->validated();

        $event = $organization->ownedEvents()->create([
            "title" => $data["title"],
            "description" => $data["description"] ?? null,
            "start" => $data["start_time"],
            "end" => $data["end_time"],
            "location" => $data["location"] ?? null,
            "address" => $data["address"] ?? null,
            "latitude" => $data["latitude"] ?? null,
            "longitude" => $data["longitude"] ?? null,
            "is_paid" => $data["is_paid"],
            "price" => $data["price"] ?? null,
            "status" => $data["status"],
            "image_url" => $data["image_url"] ?? null,
            "age_category" => $data["age_category"] ?? null,
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
        if (
            $event->owner_type !== Organization::class ||
            $event->owner_id !== $organization->id
        ) {
            abort(Status::HTTP_NOT_FOUND);
        }

        $data = $request->validated();

        $event->update([
            "title" => $data["title"] ?? $event->title,
            "description" => $data["description"] ?? $event->description,
            "start" => $data["start_time"] ?? $event->start,
            "end" => $data["end_time"] ?? $event->end,
            "location" => $data["location"] ?? $event->location,
            "address" => $data["address"] ?? $event->address,
            "latitude" => $data["latitude"] ?? $event->latitude,
            "longitude" => $data["longitude"] ?? $event->longitude,
            "is_paid" => $data["is_paid"] ?? $event->is_paid,
            "price" => $data["price"] ?? $event->price,
            "status" => $data["status"] ?? $event->status,
            "image_url" => $data["image_url"] ?? $event->image_url,
            "age_category" => $data["age_category"] ?? $event->age_category,
        ]);

        $event->loadOwnerRelations();

        return response()->json([
            "message" => __("events.updated"),
            "data" => new EventResource($event),
        ]);
    }

    public function destroy(Organization $organization, Event $event): JsonResponse
    {
        if (
            $event->owner_type !== Organization::class ||
            $event->owner_id !== $organization->id
        ) {
            abort(Status::HTTP_NOT_FOUND);
        }

        $event->delete();

        return response()->json([
            "message" => __("events.deleted_from_organization"),
        ]);
    }
}
