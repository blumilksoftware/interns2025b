<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "first_name" => ["required", "string", "max:225"],
            "last_name" => ["required", "string", "max:225"],
            "email" => ["required", "email", "max:225"],
            "password" => ["required", "confirmed", Password::min(8)],
        ];
    }
}
