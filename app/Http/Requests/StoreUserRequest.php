<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Interns2025b\DTO\RegisterUserDto;

class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "first_name" => ["required", "string", "max:225"],
            "last_name" => ["nullable", "string", "max:225"],
            "email" => ["required", "string", "email", "max:225", "unique:users,email"],
            "password" => ["required", "confirmed", Password::min(8)],
            "organization_ids" => ["array", "nullable"],
            "organization_ids.*" => ["exists:organizations,id"],
        ];
    }

    public function toDto(): RegisterUserDto
    {
        return new RegisterUserDto(
            firstName: $this->input("first_name"),
            lastName: $this->input("last_name"),
            email: $this->input("email"),
            password: $this->input("password"),
        );
    }
}
