<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "email" => "required|email|max:225",
            "password" => ["required", "confirmed", Password::min(8)],
            "token" => "required|string",
        ];
    }
}
