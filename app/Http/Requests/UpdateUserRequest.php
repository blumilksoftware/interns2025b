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
        $rules = [
            "first_name" => ["sometimes", "string", "max:225"],
            "last_name" => ["nullable", "string", "max:255"],
        ];

        if ($this->user()?->hasRole(["admin", "superAdministrator", "Administrator"])) {
            $rules["email"] = [
                "sometimes",
                "email",
                "max:225",
                Rule::unique("users", "email")->ignore($this->route("user")?->id),
            ];
            $rules["password"] = ["nullable", "confirmed", Password::min(8)];
            $rules["organization_ids"] = ["nullable", "array"];
            $rules["organization_ids.*"] = ["exists:organizations,id"];
        }

        return $rules;
    }
}
