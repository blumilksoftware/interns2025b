<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name" => ["required", "string", "max:225"],
            "owner_id" => ["nullable", "exists:users,id"],
            "group_url" => ["nullable", "url"],
            "avatar_url" => ["nullable", "url"],
        ];
    }
}
