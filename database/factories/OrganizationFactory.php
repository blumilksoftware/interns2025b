<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Interns2025b\Models\Organization;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        return [
            "name" => fake()->company,
            "group_url" => fake()->url(),
            "avatar_url" => fake()->imageUrl(200, 200, "business"),
        ];
    }
}
