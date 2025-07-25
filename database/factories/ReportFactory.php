<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Interns2025b\Models\Event;
use Interns2025b\Models\Organization;
use Interns2025b\Models\Report;
use Interns2025b\Models\User;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        $reportables = [
            User::class,
            Organization::class,
            Event::class,
        ];

        $reportableType = $this->faker->randomElement($reportables);
        $reportableId = $reportableType::factory()->create()->id;

        return [
            "reporter_id" => User::factory()->create()->id,
            "reportable_type" => $reportableType,
            "reportable_id" => $reportableId,
            "reason" => $this->faker->sentence(),
            "created_at" => now(),
            "updated_at" => now(),
        ];
    }
}
