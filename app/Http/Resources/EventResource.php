<?php

declare(strict_types=1);

namespace Interns2025b\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "start" => $this->start?->toIso8601String(),
            "end" => $this->end?->toIso8601String(),
            "location" => $this->location,
            "address" => $this->address,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            "is_paid" => $this->is_paid,
            "price" => $this->price,
            "status" => $this->status,
            "image_url" => $this->image_url,
            "age_category" => $this->age_category,
            "owner_type" => $this->owner_type,
            "owner_id" => $this->owner_id,
            "owner" => $this->whenLoaded("owner", fn(): OwnerResource => new OwnerResource($this->owner)),
            "created_at" => $this->created_at?->toIso8601String(),
            "updated_at" => $this->updated_at?->toIso8601String(),
        ];
    }
}
