<?php

declare(strict_types=1);

namespace Interns2025b\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "avatar_url" => $this->avatar_url,
            "email" => $this->email,
            "events" => EventResource::collection($this->whenLoaded("ownedEvents")),
            "events_count" => $this->ownedEvents()->count(),
            "followers_count" => $this->followers()->count(),
            "following_count" => $this->followingUsers()->count(),
        ];
    }
}
