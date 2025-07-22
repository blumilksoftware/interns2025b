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

        $randomLatitude = fn(): float => 51.2081617 + (mt_rand(-10000, 10000) / 1_000_000);
        $randomLongitude = fn(): float => 16.1603187 + (mt_rand(-10000, 10000) / 1_000_000);

        Event::factory(10)->make()->each(function (Event $event) use ($users, $randomLatitude, $randomLongitude): void {
            $user = $users->random();
            $event->owner_id = $user->id;
            $event->owner_type = User::class;
            $event->latitude = $randomLatitude();
            $event->longitude = $randomLongitude();
            $event->save();
        });

        Event::factory(10)->make()->each(function (Event $event) use ($organizations, $randomLatitude, $randomLongitude): void {
            $organization = $organizations->random();
            $event->owner_id = $organization->id;
            $event->owner_type = Organization::class;
            $event->latitude = $randomLatitude();
            $event->longitude = $randomLongitude();
            $event->save();
        });
    }
}
