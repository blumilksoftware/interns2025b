<?php

declare(strict_types=1);

namespace Interns2025b\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "email" => $this->email,
            "facebook_linked" => $this->facebook_id !== null,
            "email_verified_at" => $this->email_verified_at,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "events_count" => $this->ownedEvents()->count(),
            "followers_count" => $this->followers()->count(),
            "following_count" => $this->followingUsers()->count(),
        ];
    }
}
