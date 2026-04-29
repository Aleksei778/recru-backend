<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Interview\Models\Question;
use App\VoiceLog\Enum\Type;
use App\VoiceLog\Models\VoiceLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoiceLogFactory extends Factory
{
    protected $model = VoiceLog::class;

    public function definition(): array
    {
        $question = Question::factory()->create();

        return [
            'subject_type' => Question::class,
            'subject_id' => $question->id,
            'audio_path' => 'voice/' . fake()->uuid() . '.mp3',
            'type' => fake()->randomElement(Type::cases())->value,
            'mime_type' => 'audio/mpeg',
        ];
    }
}