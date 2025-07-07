<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "first_name" => ["required", "string", "max:225"],
            "last_name" => ["nullable", "string", "max:225"],
        ];
    }
}
