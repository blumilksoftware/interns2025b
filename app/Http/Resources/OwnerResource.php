<?php

declare(strict_types=1);

namespace Interns2025b\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;

class OwnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return match (true) {
            $this->resource instanceof User => (new UserResource($this->resource))->toArray($request),
            $this->resource instanceof Organization => (new OrganizationResource($this->resource))->toArray($request),
            default => [],
        };
    }
}
