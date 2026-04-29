<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Ai\Operation\Enum\{Status, Type};
use App\Ai\Operation\Models\Operation;
use App\Interview\Models\Interview;
use App\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

final class OperationFactory extends Factory
{
    protected $model = Operation::class;

    public function definition(): array
    {
        $interview = Interview::factory()->create();

        return [
            'subject_type' => Interview::class,
            'subject_id' => $interview->id,
            'type' => fake()->randomElement(Type::cases())->value,
            'tenant_id' => Tenant::factory(),
            'status' => fake()->randomElement(Status::cases())->value,
            'provider_id' => fake()->optional(0.7)->uuid(),
            'provider' => fake()->randomElement(['openai', 'anthropic', 'yandex']),
            'raw_request' => ['model' => 'gpt-4', 'messages' => []],
            'raw_response' => fake()->optional(0.6)->passthrough(['choices' => []]),
            'result' => fake()->optional(0.6)->paragraph(),
        ];
    }

    public function completed(): static
    {
        return $this->state([
            'status' => Status::Completed->value,
            'raw_response' => ['choices' => [['message' => ['content' => fake()->paragraph()]]]],
            'result' => fake()->paragraph(),
        ]);
    }
}
