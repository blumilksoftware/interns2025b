<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Interns2025b\DTO\UpdateUserDto;

class UpdateAdminRequest extends FormRequest
{
    public function rules(): array
    {
        $adminId = $this->route("admin")?->id;

        return [
            "first_name" => ["sometimes", "string", "max:225"],
            "last_name" => ["nullable", "string", "max:225"],
            "avatar_url" => ["nullable", "url", "max:2048"],
            "email" => ["sometimes", "email", "max:225", Rule::unique("users", "email")->ignore($adminId)],
        ];
    }

    public function toDto(): UpdateUserDto
    {
        return new UpdateUserDto(
            $this->input("first_name"),
            $this->input("last_name"),
            $this->input("avatar_url"),
            $this->input("email"),
        );
    }
}
