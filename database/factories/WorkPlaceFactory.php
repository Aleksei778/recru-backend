<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Candidate\Models\{Candidate, WorkPlace};
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkPlaceFactory extends Factory
{
    protected $model = WorkPlace::class;

    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-10 years', '-1 year');
        $endedAt = fake()->optional(0.7)->dateTimeBetween($startedAt, 'now');

        return [
            'candidate_id' => Candidate::factory(),
            'company_name' => fake()->company(),
            'position' => fake()->jobTitle(),
            'description' => fake()->optional(0.6)->paragraph(),
            'started_at' => $startedAt->format('Y-m-d'),
            'ended_at' => $endedAt?->format('Y-m-d'),
        ];
    }
}