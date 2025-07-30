<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            "type" => "required|in:user,organization,event",
            "id" => "required|integer",
            "reason" => "nullable|string|max:1000",
        ];
    }
}
