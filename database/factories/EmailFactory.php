<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Candidate\Models\Candidate;
use App\Common\Enum\Locale;
use App\Email\Enum\{Status, Type};
use App\Email\Models\Email;
use App\Interview\Models\Interview;
use App\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailFactory extends Factory
{
    protected $model = Email::class;

    public function definition(): array
    {
        $candidate = Candidate::factory()->create();

        return [
            'interview_id' => Interview::factory(),
            'sender_id' => User::factory(),
            'recipient_type' => Candidate::class,
            'recipient_id' => $candidate->id,
            'status' => fake()->randomElement(Status::cases())->value,
            'type' => fake()->randomElement(Type::cases())->value,
            'locale' => fake()->randomElement(Locale::cases())->value,
            'subject' => fake()->sentence(),
            'sent_at' => fake()->optional(0.8)->dateTimeBetween('-1 month', 'now'),
        ];
    }
}