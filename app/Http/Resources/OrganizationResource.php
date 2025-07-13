<?php

declare(strict_types=1);

namespace Interns2025b\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "group_url" => $this->group_url,
            "avatar_url" => $this->avatar_url,
            "owner" => [
                "id" => $this->owner?->id,
                "first_name" => $this->owner?->first_name,
                "last_name" => $this->owner?->last_name,
            ],
            "users" => $this->users->map(fn($user): array => [
                "id" => $user->id,
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
            ]),
        ];
    }
}
