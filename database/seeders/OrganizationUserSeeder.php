<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Interns2025b\Models\Organization;
use Interns2025b\Models\User;

class OrganizationUserSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::factory()->create();
        $organization = Organization::factory()->create([
            "owner_id" => $owner->id,
        ]);

        $organization->users()->attach($owner->id);
        $additionalUsers = User::factory()->count(10)->create();
        $organization->users()->attach($additionalUsers->pluck("id")->toArray());
    }
}
