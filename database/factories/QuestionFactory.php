<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Interview\Models\{Interview, Question};
use Illuminate\Database\Eloquent\Factories\Factory;

final class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'interview_id' => Interview::factory(),
            'text' => fake()->sentence() . '?',
            'number' => fake()->numberBetween(1, 10),
        ];
    }
}
