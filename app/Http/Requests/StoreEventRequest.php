<?php

declare(strict_types=1);

namespace Interns2025b\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Interns2025b\Enums\EventStatus;
use Interns2025b\Enums\Role;

class StoreEventRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "title" => ["required", "string", "max:255"],
            "description" => ["nullable", "string"],
            "start_time" => ["required", "date", "before:end_time"],
            "end_time" => ["required", "date", "after:start_time"],
            "location" => ["required", "string"],
            "address" => ["nullable", "string"],
            "latitude" => ["nullable", "numeric"],
            "longitude" => ["nullable", "numeric"],
            "is_paid" => ["required", "boolean"],
            "price" => ["nullable", "numeric", "min:0"],
            "status" => ["required", Rule::enum(EventStatus::class)],
            "image_url" => ["nullable", "string", "url"],
            "age_category" => ["nullable", "string"],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $user = $this->user();
            $status = $this->input("status");

            if ($user->hasRole(Role::User->value) &&
                in_array($status, [EventStatus::Published->value, EventStatus::Ongoing->value], true)
            ) {
                $hasActiveEvent = $user->ownedEvents()
                    ->whereIn("status", [EventStatus::Published->value, EventStatus::Ongoing->value])
                    ->exists();

                if ($hasActiveEvent) {
                    $validator->errors()->add("status", __("events.limit_reached"));
                }
            }
        });
    }
}
