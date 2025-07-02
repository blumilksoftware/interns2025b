<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "title" => ["sometimes", "string", "max:255"],
            "description" => ["nullable", "string"],
            "start" => ["nullable", "date"],
            "end" => ["nullable", "date"],
            "location" => ["nullable", "string"],
            "address" => ["nullable", "string"],
            "latitude" => ["nullable", "numeric"],
            "longitude" => ["nullable", "numeric"],
            "is_paid" => ["boolean"],
            "price" => ["nullable", "numeric"],
            "status" => ["sometimes", "in:draft,published,ongoing,ended,canceled"],
            "image_url" => ["nullable", "string"],
            "age_category" => ["nullable", "string"],
            "owner_type" => ["sometimes", "string"],
            "owner_id" => ["sometimes", "integer"],
        ];
    }
}
