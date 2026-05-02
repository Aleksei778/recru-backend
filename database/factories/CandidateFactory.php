<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Candidate\Enum\{EducationLevel, Grade, Source, Status};
use App\Candidate\Models\Candidate;
use App\Common\Enum\Locale;
use App\Tenant\Models\Tenant;
use App\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class CandidateFactory extends Factory
{
    protected $model = Candidate::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'middle_name' => fake()->optional(0.6)->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional(0.8)->phoneNumber(),
            'source' => fake()->randomElement(Source::cases())->value,
            'grade' => fake()->randomElement(Grade::cases())?->value,
            'status' => fake()->randomElement(Status::cases())->value,
            'experience_years' => fake()->numberBetween(0, 20),
            'education_level' => fake()->randomElement(EducationLevel::cases())?->value,
            'added_by_id' => User::factory(),
            'locale' => fake()->randomElement(Locale::cases())->value,
        ];
    }

    public function forTenant(Tenant $tenant, User $user): static
    {
        return $this->state([
            'tenant_id' => $tenant->id,
            'added_by_id' => $user->id,
        ]);
    }
}
