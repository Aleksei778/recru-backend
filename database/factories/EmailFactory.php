<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Candidate\Models\Candidate;
use App\Common\Enum\Locale;
use App\Email\Enum\Type;
use App\Email\Models\Email;
use App\Interview\Models\Interview;
use App\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class EmailFactory extends Factory
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
            'type' => fake()->randomElement(Type::cases())->value,
            'locale' => fake()->randomElement(Locale::cases())->value,
            'subject' => fake()->sentence(),
            'sent_at' => fake()->optional(0.8)->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function sent(): static
    {
        return $this->state([
            'sent_at' => now(),
        ]);
    }

    public function forInterview(Interview $interview, User $sender): static
    {
        return $this->state([
            'interview_id' => $interview->id,
            'sender_id' => $sender->id,
            'recipient_type' => Candidate::class,
            'recipient_id' => $interview->candidate_id,
            'locale' => fake()->randomElement(Locale::cases())->value,
        ]);
    }
}
