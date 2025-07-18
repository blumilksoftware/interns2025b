<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Interns2025b\Models\Event;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory(10)->create();
        $organizations = Organization::factory(10)->create();

        $randomLatitude = fn(): float => mt_rand(1100000, 1200000) / 10000000 + 51.0;
        $randomLongitude = fn(): float => mt_rand(1610000, 1620000) / 10000000 + 16.0;

        Event::factory(10)->make()->each(function ($event) use ($users, $randomLatitude, $randomLongitude): void {
            $user = $users->random();
            $event->owner_id = $user->id;
            $event->owner_type = User::class;
            $event->latitude = $randomLatitude();
            $event->longitude = $randomLongitude();
            $event->save();
        });

        Event::factory(10)->make()->each(function ($event) use ($organizations, $randomLatitude, $randomLongitude): void {
            $organization = $organizations->random();
            $event->owner_id = $organization->id;
            $event->owner_type = Organization::class;
            $event->latitude = $randomLatitude();
            $event->longitude = $randomLongitude();
            $event->save();
        });
    }
}
