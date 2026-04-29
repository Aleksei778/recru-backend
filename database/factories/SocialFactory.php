<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Candidate\Models\{Candidate, Social};
use Illuminate\Database\Eloquent\Factories\Factory;

final class SocialFactory extends Factory
{
    protected $model = Social::class;

    public function definition(): array
    {
        $networks = ['linkedin', 'github', 'telegram', 'hh', 'habr'];

        return [
            'candidate_id' => Candidate::factory(),
            'name' => fake()->randomElement($networks),
            'url' => fake()->url(),
        ];
    }
}
