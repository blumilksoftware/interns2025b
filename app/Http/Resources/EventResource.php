<?php

declare(strict_types=1);

namespace Interns2025b\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "start" => $this->start,
            "end" => $this->end,
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
        ];
    }
}
