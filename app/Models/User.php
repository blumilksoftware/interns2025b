<?php

declare(strict_types=1);

namespace Interns2025b\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property Carbon $email_verified_at
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
        "email",
        "password",
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

    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
        ];
    }
}
