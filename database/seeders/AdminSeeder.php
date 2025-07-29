<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Interns2025b\Enums\BadgeType;
use Interns2025b\Models\Badge;
use Interns2025b\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ["email" => "admin@example.com"],
            [
                "first_name" => "Admin",
                "last_name" => "Admin_Last_Name",
                "email_verified_at" => now(),
                "password" => Hash::make("password"),
                "remember_token" => Str::random(10),
            ],
        );
        $admin->assignRole("administrator");
        $admin->badge()->associate(Badge::query()->where("type", BadgeType::CityExplorer)->first());
        $admin->save();

        $superAdmin = User::firstOrCreate(
            ["email" => "superadmin@example.com"],
            [
                "first_name" => "Super_Admin",
                "last_name" => "Super_Admin_Last_Name",
                "email_verified_at" => now(),
                "password" => Hash::make("password"),
                "remember_token" => Str::random(10),
            ],
        );
        $superAdmin->assignRole("superAdministrator");
        $superAdmin->badge()->associate(Badge::query()->where("type", BadgeType::UrbanLegend)->first());
        $superAdmin->save();
    }
}
