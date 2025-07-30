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
            "events_count" => $this->owned_events_count,
            "followers_count" => $this->followers_count,
            "following_count" => $this->following_users_count,
            "badge" => $this->whenLoaded("badge", optional($this->badge)?->only("name")),
        ];
    }
}
