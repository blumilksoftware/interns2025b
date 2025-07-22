<?php

declare(strict_types=1);

namespace Interns2025b\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "avatar_url" => $this->avatar_url,
            "email" => $this->email,
            "facebook_linked" => $this->facebook_id !== null,
        ];
    }
}
