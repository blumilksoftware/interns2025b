<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "first_name" => ["required", "string", "max:225"],
            "last_name" => ["required", "string", "max:225"],
            "email" => ["required", "email:rfc,dns", "max:225", "string"],
            "password" => ["required", "min:8", "max:225", "string"],
        ];
    }
}
