<?php

declare(strict_types=1);

namespace Interns2025b\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Interns2025b\Enums\BadgeType;

/**
 * @property int $id
 * @property string $name
 * @property BadgeType $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Badge extends Model
{
    protected $fillable = [
        "name",
        "type",
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    protected function casts(): array
    {
        return [
            "type" => BadgeType::class,
        ];
    }
}
