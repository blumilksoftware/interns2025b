<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $ownerType = fake()->randomElement([User::class, Organization::class]);

        $ownerId = $ownerType::inRandomOrder()->value("id") ?? $ownerType::factory()->create()->id;
        $isPaid = fake()->boolean;

        return [
            "title" => fake()->jobTitle,
            "description" => fake()->text,
            "start" => fake()->dateTimeBetween("+1 days", "+10 days"),
            "end" => fake()->dateTimeBetween("+11 days", "+20 days"),
            "location" => fake()->city,
            "address" => fake()->address,
            "latitude" => fake()->latitude,
            "longitude" => fake()->longitude,
            "is_paid" => $isPaid,
            "price" => $isPaid ? fake()->randomFloat(2, 1, 100) : 0,
            "status" => fake()->randomElement(["draft", "published", "ongoing", "ended", "canceled"]),
            "image_url" => fake()->imageUrl(),
            "age_category" => fake()->randomElement(["kids", "teens", "adults"]),
            "owner_type" => $ownerType,
            "owner_id" => $ownerId,
        ];
    }
}
