<?php

declare(strict_types=1);

namespace App\Interview\Jobs;

use App\Ai\Tts\Providers\TtsInterface;
use App\Interview\Models\Question;
use App\Interview\Services\Questions\StoragePathHelper;
use App\VoiceLog\Dto\Create as VoiceLogCreateDto;
use App\VoiceLog\Enum\Type as VoiceLogType;
use App\VoiceLog\Services\CrudService as VoiceLogCrudService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use App\Common\Services\Storage;

final class SynthesizeQuestionJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, Dispatchable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public Question $question,
    ) {
    }

    public function handle(
        TtsInterface $tts,
        StoragePathHelper $storagePathHelper,
        Storage $storage,
        VoiceLogCrudService $voiceLogCrudService,
    ): void {
        $result = $tts->synthesize($this->question->text);

        if (!$result) {
            $this->fail('TTS synthesis failed for question ' . $this->question->id);

            return;
        }

        $path = $storagePathHelper->getStoragePath($this->question);
        $storage->put(config('filesystems.default'), $path, $result->audioContent);

        $voiceLogCrudService->create(new VoiceLogCreateDto(
            subjectId: $this->question->id,
            subjectType: Question::class,
            audioPath: $path,
            mimeType: $result->mimeType,
            type: VoiceLogType::Tts,
        ));
    }
}
