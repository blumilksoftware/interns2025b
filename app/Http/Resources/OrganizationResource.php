<?php

declare(strict_types=1);

namespace Interns2025b\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "owner_id" => $this->owner_id,
            "group_url" => $this->group_url,
            "avatar_url" => $this->avatar_url,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
