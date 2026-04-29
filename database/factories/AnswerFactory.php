<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Interview\Models\{Answer, Question};
use Illuminate\Database\Eloquent\Factories\Factory;

final class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'text' => fake()->paragraphs(2, true),
        ];
    }
}
