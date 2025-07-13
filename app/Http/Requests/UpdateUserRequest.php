<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Interns2025b\DTO\UpdateUserDto;

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
            "organization_ids" => ["nullable", "array"],
            "organization_ids.*" => ["exists:organizations,id"],
        ];
    }

    public function toDto(): UpdateUserDto
    {
        return new UpdateUserDto(
            $this->input("first_name"),
            $this->input("last_name"),
            $this->input("email"),
            $this->input("organization_ids"),
        );
    }
}
