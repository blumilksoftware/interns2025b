<?php

declare(strict_types=1);

namespace Interns2025b\Enums;

enum BadgeType: string
{
    case None = "none";
    case CityExplorer = "city_explorer";
    case UrbanLegend = "urban_legend";

    public function eventLimitBonus(): int
    {
        return match ($this) {
            self::None => 0,
            self::CityExplorer => 2,
            self::UrbanLegend => 4,
        };
    }
}
