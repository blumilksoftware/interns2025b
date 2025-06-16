<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Interns2025b\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(OrganizationUserSeeder::class);

        if (User::query()->where("email", "=", "admin@example.com")->count() === 0) {
            User::factory([
                "email" => "admin@example.com",
                "password" => Hash::make("password"),
            ])->superAdmin()->create();
        }
    }
}
