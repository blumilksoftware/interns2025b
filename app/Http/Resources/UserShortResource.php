<?php

declare(strict_types=1);

namespace Interns2025b\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserShortResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => "$this->first_name $this->last_name",
        ];
    }
}
