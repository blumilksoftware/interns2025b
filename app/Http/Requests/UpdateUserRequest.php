<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "first_name" => ["sometimes", "string", "max:225"],
            "last_name" => ["nullable", "string", "max:225"],
            "email" => [
                "sometimes",
                "email",
                "max:225",
                Rule::unique("users", "email")->ignore($this->route("user")?->id),
            ],
            "password" => ["nullable", "confirmed", Password::min(8)],
            "organization_ids" => ["nullable", "array"],
            "organization_ids.*" => ["exists:organizations,id"],
        ];
    }
}
