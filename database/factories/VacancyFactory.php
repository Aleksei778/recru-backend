<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Tenant\Models\Tenant;
use App\User\Models\User;
use App\Vacancy\Enum\{EmploymentType, Status, WorkMode};
use App\Vacancy\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

final class VacancyFactory extends Factory
{
    protected $model = Vacancy::class;

    public function definition(): array
    {
        $salaryMin = fake()->optional(0.8)->numberBetween(50000, 200000);

        return [
            'tenant_id' => Tenant::factory(),
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraphs(3, true),
            'employment_type' => fake()->randomElement(EmploymentType::cases())->value,
            'work_mode' => fake()->randomElement(WorkMode::cases())->value,
            'salary_min' => $salaryMin,
            'salary_max' => $salaryMin ? fake()->numberBetween($salaryMin, $salaryMin + 100000) : null,
            'salary_currency' => fake()->randomElement(['RUB', 'USD', 'EUR']),
            'status' => fake()->randomElement(Status::cases())->value,
            'location' => fake()->optional(0.7)->city(),
            'published_at' => fake()->optional(0.6)->dateTimeBetween('-6 months', 'now'),
            'closed_at' => null,
            'created_by_id' => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state([
            'status' => Status::PUBLISHED->value,
            'published_at' => now(),
        ]);
    }

    public function forTenant(Tenant $tenant, User $user): static
    {
        return $this->state([
            'tenant_id' => $tenant->id,
            'created_by_id' => $user->id,
        ]);
    }
}
