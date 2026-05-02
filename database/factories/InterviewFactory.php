<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Candidate\Models\Candidate;
use App\Interview\Enum\Status;
use App\Interview\Models\Interview;
use App\Vacancy\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class InterviewFactory extends Factory
{
    protected $model = Interview::class;

    public function definition(): array
    {
        return [
            'candidate_id' => Candidate::factory(),
            'vacancy_id' => Vacancy::factory(),
            'status' => fake()->randomElement(Status::cases())->value,
            'token' => Str::uuid(),
            'token_expires_at' => fake()->dateTimeBetween('now', '+7 days'),
            'grade' => fake()->optional(0.4)->numberBetween(1, 10),
            'text_grade' => fake()->optional(0.4)->paragraph(),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => Status::Pending->value]);
    }

    public function evaluated(): static
    {
        return $this->state([
            'status' => Status::Evaluated->value,
            'grade' => fake()->numberBetween(1, 10),
            'text_grade' => fake()->paragraph(),
        ]);
    }
}
