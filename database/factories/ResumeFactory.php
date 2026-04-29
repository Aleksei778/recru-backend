<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Candidate\Models\Candidate;
use App\Resume\Models\Resume;
use App\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ResumeFactory extends Factory
{
    protected $model = Resume::class;

    public function definition(): array
    {
        $fileName = fake()->slug(3) . '.pdf';

        return [
            'candidate_id' => Candidate::factory(),
            'file_path' => 'resumes/' . $fileName,
            'file_name' => $fileName,
            'mime_type' => 'application/pdf',
            'size' => fake()->numberBetween(50000, 5000000),
            'storage_disk' => 's3',
            'parsed_data' => fake()->optional(0.7)->passthrough([
                'skills' => fake()->words(5),
                'experience' => fake()->numberBetween(0, 15),
            ]),
            'text_grade' => fake()->optional(0.5)->paragraph(),
            'grade' => fake()->optional(0.5)->numberBetween(1, 10),
            'saved_by_id' => User::factory(),
        ];
    }
}
