<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Interns2025b\Enums\EventStatus;

class StoreEventRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "title" => ["required", "string", "max:255"],
            "description" => ["nullable", "string"],
            "start" => ["required", "date", "before:end"],
            "end" => ["required", "date", "after:start"],
            "location" => ["required", "string"],
            "address" => ["nullable", "string"],
            "latitude" => ["nullable", "numeric"],
            "longitude" => ["nullable", "numeric"],
            "is_paid" => ["required", "boolean"],
            "price" => ["nullable", "numeric", "min:0"],
            "status" => ["required", Rule::enum(EventStatus::class)],
            "image_url" => ["nullable", "string", "url"],
            "age_category" => ["nullable", "string"],
        ];
    }
}
