<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'id' => Str::slug($name) . '-' . fake()->unique()->numerify('###'),
            'name' => $name,
            'subdomain' => Str::slug($name) . fake()->unique()->numerify('##'),
            'website' => fake()->url(),
            'industry' => fake()->randomElement(['tech', 'finance', 'healthcare', 'retail', 'education', 'manufacturing']),
        ];
    }
}
