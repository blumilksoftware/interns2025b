<?php

declare(strict_types=1);

namespace Interns2025b\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "reporter_id" => $this->reporter_id,
            "reportable_type" => class_basename($this->reportable_type),
            "reportable_id" => $this->reportable_id,
            "reason" => $this->reason,
            "created_at" => $this->created_at->toISOString(),
        ];
    }
}
