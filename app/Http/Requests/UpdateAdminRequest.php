<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateAdminRequest extends FormRequest
{
    public function rules(): array
    {
        $adminId = $this->route("admin")?->id;

        return [
            "first_name" => ["sometimes", "string", "max:225"],
            "last_name" => ["nullable", "string", "max:225"],
            "email" => ["sometimes", "email", "max:225", Rule::unique("users", "email")->ignore($adminId)],
            "password" => ["nullable", "confirmed", Password::min(8)],
        ];
    }
}
