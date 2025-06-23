<?php

declare(strict_types=1);

namespace Interns2025b\Enums;

enum Role: string
{
    case User = "user";
    case SuperAdministrator = "superAdministrator";
    case Administrator = "administrator";

    public static function casesToSelect(): array
    {
        $cases = collect(Role::cases());

        return $cases->map(
            fn(Role $enum): array => [
                "label" => $enum->label(),
                "value" => $enum->value,
            ],
        )->toArray();
    }

    public function label(): string
    {
        return $this->value;
    }
}
