<?php

declare(strict_types=1);

namespace Interns2025b\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isSelf = Auth::check() && Auth::id() === $this->id;

        return [
            "id" => $this->id,
            "full_name" => $this->full_name,
            "email" => $this->email,
            "events" => EventResource::collection($this->events),
            "events_count" => $this->ownedEvents()->count(),
            "followers_count" => $this->followers()->count(),
            "following_count" => $this->followingUsers()->count(),
        ];
    }
}
