<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Interns2025b\Enums\BadgeType;
use Interns2025b\Models\Badge;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (BadgeType::cases() as $type) {
            if ($type === BadgeType::None) {
                continue;
            }

            Badge::firstOrCreate([
                "name" => ucfirst(str_replace("_", " ", $type->value)),
                "type" => $type->value,
            ]);
        }
    }
}
