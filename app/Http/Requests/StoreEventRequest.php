<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "title" => ["required", "string", "max:255"],
            "description" => ["nullable", "string"],
            "start_time" => ["required", "date", "before:end_time"],
            "end_time" => ["required", "date", "after:start_time"],
            "location" => ["required", "string"],
            "address" => ["nullable", "string"],
            "latitude" => ["nullable", "numeric"],
            "longitude" => ["nullable", "numeric"],
            "is_paid" => ["required", "boolean"],
            "price" => ["nullable", "numeric", "min:0"],
            "status" => ["required", "in:draft,published,ongoing,ended,canceled"],
            "image_url" => ["nullable", "string", "url"],
            "age_category" => ["nullable", "string"],
        ];
    }
}
