<?php

declare(strict_types=1);

namespace Interns2025b\Enums;

enum EventStatus: string
{
    case Draft = "draft";
    case Published = "published";
    case Ongoing = "ongoing";
    case Ended = "ended";
    case Canceled = "canceled";

    public function label(): string
    {
        return __("enums.event_status." . $this->value);
    }

    public static function options(): array
    {
        return collect(self::cases())->map(fn(self $status): array => [
            "label" => $status->label(),
            "value" => $status->value,
        ])->toArray();
    }
}
