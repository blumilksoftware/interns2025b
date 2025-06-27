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

        Event::factory(10)->make()->each(function ($event) use ($users): void {
            $user = $users->random();
            $event->owner_id = $user->id;
            $event->owner_type = User::class;
            $event->save();
        });

        Event::factory(10)->make()->each(function ($event) use ($organizations): void {
            $organization = $organizations->random();
            $event->owner_id = $organization->id;
            $event->owner_type = Organization::class;
            $event->save();
        });
    }
}
