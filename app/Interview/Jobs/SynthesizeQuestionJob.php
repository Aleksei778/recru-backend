<?php

declare(strict_types=1);

namespace App\Interview\Jobs;

use App\Ai\Tts\Contracts\SyncInterface;
use App\Interview\Models\Question;
use App\Interview\Services\QuestionsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\Storage;

final class SynthesizeQuestionJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, Dispatchable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public Question $question,
    ) {
    }

    public function handle(
        SyncInterface $tts,
        QuestionsService $questionsService,
    ): void {
        $result = $tts->synthesize($this->question->text);

        if (!$result) {
            $this->fail('TTS synthesis failed for question ' . $this->question->id);

            return;
        }

        $path = $questionsService->getStoragePath($this->question->id);
        Storage::disk('yandex_object_storage')->put($path, $result->audioContent);

        $this->question->update(['audio_path' => $path]);
    }
}
