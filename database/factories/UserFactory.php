<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Common\Enum\Locale;
use App\Tenant\Models\Tenant;
use App\User\Enum\UserRole;
use App\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => fake()->randomElement(UserRole::cases())->value,
            'locale' => fake()->randomElement(Locale::cases())->value,
            'tenant_id' => Tenant::factory(),
        ];
    }

    public function admin(): static
    {
        return $this->state(['role' => UserRole::ADMIN->value]);
    }

    public function hr(): static
    {
        return $this->state(['role' => UserRole::HR->value]);
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(['tenant_id' => $tenant->id]);
    }

    public function unverified(): static
    {
        return $this->state(['email_verified_at' => null]);
    }
}