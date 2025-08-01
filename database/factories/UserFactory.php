<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Interns2025b\Enums\BadgeType;
use Interns2025b\Enums\Role;
use Interns2025b\Models\Badge;
use Interns2025b\Models\User;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            "first_name" => fake()->firstName(),
            "last_name" => fake()->lastName(),
            "avatar_url" => $this->faker->imageUrl(200, 200),
            "email" => fake()->unique()->safeEmail(),
            "email_verified_at" => now(),
            "password" => Hash::make("password"),
            "remember_token" => Str::random(10),
            "facebook_id" => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            $user->assignRole("user");
        });
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes): array => [
            "email_verified_at" => null,
        ]);
    }

    public function admin(): static
    {
        return $this->afterCreating(function (User $user): void {
            $user->syncRoles(Role::Administrator);
        });
    }

    public function superAdmin(): static
    {
        return $this->afterCreating(function (User $user): void {
            $user->syncRoles(Role::SuperAdministrator);
        });
    }

    public function cityExplorer(): static
    {
        return $this->afterCreating(function (User $user): void {
            $badge = Badge::query()->where("type", BadgeType::CityExplorer)->first();
            $user->badge()->associate($badge)->save();
        });
    }

    public function urbanLegend(): static
    {
        return $this->afterCreating(function (User $user): void {
            $badge = Badge::query()->where("type", BadgeType::UrbanLegend)->first();
            $user->badge()->associate($badge)->save();
        });
    }
}
