<?php

declare(strict_types=1);

namespace Interns2025b\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $first_name
 * @property ?string $last_name
 * @property ?string $avatar_url
 * @property string $email
 * @property ?string $full_name
 * @property ?string $password
 * @property ?string $facebook_id
 * @property ?Carbon $email_verified_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;

    protected $fillable = [
        "first_name",
        "last_name",
        "avatar_url",
        "email",
        "password",
        "facebook_id",
    ];
    protected $hidden = [
        "password",
        "remember_token",
    ];

    public function ownedOrganizations(): HasMany
    {
        return $this->hasMany(Organization::class, "owner_id");
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, "organization_user");
    }

    public function ownedEvents(): MorphMany
    {
        return $this->morphMany(Event::class, "owner");
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, "event_user");
    }

    public function followingUsers(): MorphToMany
    {
        return $this->morphedByMany(
            self::class,
            "followable",
            "followables",
            "followable_id",
            "user_id",
        );
    }

    public function followingOrganizations(): MorphToMany
    {
        return $this->morphedByMany(Organization::class, "followable", "followables");
    }

    public function followingEvents(): MorphToMany
    {
        return $this->morphedByMany(Event::class, "followable", "followables");
    }

    public function followers(): MorphToMany
    {
        return $this->morphedByMany(
            self::class,
            "followable",
            "followables",
            "user_id",
            "followable_id",
        );
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    public function eventLimit(): int
    {
        if (!$this->relationLoaded("badge")) {
            $this->load("badge");
        }

        $bonus = $this->badge?->type->eventLimitBonus() ?? 0;

        return 1 + $bonus;
    }

    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
        ];
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn(): string => trim("$this->first_name $this->last_name"),
        );
    }
}
