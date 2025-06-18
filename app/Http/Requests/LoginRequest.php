<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "email" => ["required", "email:rfc,dns", "max:225", "string"],
            "password" => ["required", "min:8", "max:225", "string"],
        ];
    }
}
