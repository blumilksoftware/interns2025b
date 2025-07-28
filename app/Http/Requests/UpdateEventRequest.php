<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Interns2025b\Enums\EventStatus;

class UpdateEventRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "title" => ["sometimes", "string", "max:255"],
            "description" => ["nullable", "string"],
            "start" => ["sometimes", "date", "before:end"],
            "end" => ["sometimes", "date", "after:start"],
            "location" => ["sometimes", "string"],
            "address" => ["nullable", "string"],
            "latitude" => ["nullable", "numeric"],
            "longitude" => ["nullable", "numeric"],
            "is_paid" => ["sometimes", "boolean"],
            "price" => ["nullable", "numeric", "min:0"],
            "status" => ["sometimes", Rule::enum(EventStatus::class)],
            "image_url" => ["nullable", "string", "url"],
            "age_category" => ["nullable", "string"],
            "owner_type" => ["sometimes", "string"],
            "owner_id" => ["sometimes", "integer"],
        ];
    }
}
