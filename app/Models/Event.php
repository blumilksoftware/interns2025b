<?php

declare(strict_types=1);

namespace Interns2025b\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Interns2025b\Enums\EventStatus;

/**
 * @property int $id
 * @property string $title
 * @property ?string $description
 * @property ?Carbon $start
 * @property ?Carbon $end
 * @property ?string $location
 * @property ?string $address
 * @property ?float $latitude
 * @property ?float $longitude
 * @property bool $is_paid
 * @property ?float $price
 * @property EventStatus $status
 * @property ?string $image_url
 * @property ?string $age_category
 * @property string $owner_type
 * @property int $owner_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "description",
        "start",
        "end",
        "location",
        "address",
        "latitude",
        "longitude",
        "is_paid",
        "price",
        "status",
        "image_url",
        "age_category",
        "owner_type",
        "owner_id",
    ];
    protected $casts = [
        "start" => "datetime",
        "end" => "datetime",
        "latitude" => "float",
        "longitude" => "float",
        "is_paid" => "boolean",
        "price" => "float",
        "owner_id" => "integer",
        "status" => EventStatus::class,
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "event_user");
    }

    public function followers(): MorphToMany
    {
        return $this->morphToMany(User::class, "followable", "followables");
    }

    public function loadOwnerRelations(): self
    {
        $this->loadMissing("owner");

        if ($this->owner instanceof Organization) {
            $this->owner->loadMissing("owner");
        }

        return $this;
    }

    public static function loadWithOwnerRelations($query = null): Collection
    {
        $query ??= static::query();

        $events = $query->with("owner")->get();
        $events->loadMorph("owner", [
            Organization::class => ["owner"],
        ]);

        return $events;
    }
}
