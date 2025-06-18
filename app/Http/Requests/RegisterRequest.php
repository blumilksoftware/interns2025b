<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "first_name" => ["required", "string", "max:225"],
            "last_name" => ["nullable", "string", "max:225"],
            "email" => ["required", "email:rfc,dns", "max:225", "string"],
            "password" => ["required", "confirmed", Password::min(8)],
        ];
    }
}
