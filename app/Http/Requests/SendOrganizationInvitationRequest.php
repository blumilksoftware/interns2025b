<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendOrganizationInvitationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "email" => "required|email|exists:users,email",
        ];
    }
}
