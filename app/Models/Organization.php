<?php

declare(strict_types=1);

namespace Interns2025b\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $group_url
 * @property string|null $avatar_url
 * @property int|null $owner_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "group_url",
        "avatar_url",
        "owner_id",
    ];
    protected $casts = [
        "owner_id" => "integer",
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, "owner_id");
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "organization_user");
    }
}
