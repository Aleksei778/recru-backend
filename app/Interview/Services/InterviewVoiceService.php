<?php

declare(strict_types=1);

namespace App\Interview\Services;

use App\Ai\Yandex\Dto\ObjectStorage as ObjectStorageDto;
use App\Ai\Yandex\Dto\Tts\Request as TtsRequest;
use App\Ai\Yandex\Services\ObjectStorageService;
use App\Ai\Yandex\Services\Speechkit\SttService;
use App\Ai\Yandex\Services\Speechkit\TtsService;
use App\Interview\Models\Answer;
use App\Interview\Models\Interview;
use App\Interview\Models\Question;
use App\VoiceLog\Models\VoiceLog;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

final readonly class InterviewVoiceService
{
    public function __construct(
        private TtsService $ttsService,
        private SttService $sttService,
        private ObjectStorageService $storageService,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Озвучивает вопрос и сохраняет аудио в VoiceLog.
     */
    public function synthesizeQuestion(Question $question): ?string
    {
        $request = new TtsRequest(text: $question->text);
        $result = $this->ttsService->synthesize($request);

        if (!$result) {
            $this->logger->error('Failed to synthesize question', ['question_id' => $question->id]);
            return null;
        }

        $dto = new ObjectStorageDto(
            content: $result->audioContent,
            mimeType: $result->mimeType,
            fileId: "question_{$question->id}",
            fileSize: (string)strlen($result->audioContent)
        );

        $key = $this->storageService->uploadFile($dto);

        $question->voiceLog()->updateOrCreate([], [
            'status' => 'completed',
            'mimetype' => $result->mimeType,
            'size' => strlen($result->audioContent),
            'yandex_id' => $key, // Используем как ключ в S3
        ]);

        return $this->storageService->getObjectUri($key);
    }

    /**
     * Обрабатывает аудио-ответ кандидата: загружает в S3 и отправляет на распознавание.
     */
    public function handleAnswerAudio(Question $question, string $audioContent, string $mimeType): ?string
    {
        $answer = Answer::updateOrCreate(
            ['question_id' => $question->id],
            ['text' => ''] // Текст будет заполнен после STT
        );

        $dto = new ObjectStorageDto(
            content: $audioContent,
            mimeType: $mimeType,
            fileId: "answer_{$answer->id}",
            fileSize: (string)strlen($audioContent)
        );

        $key = $this->storageService->uploadFile($dto);
        $operationId = $this->sttService->send($key);

        if ($operationId) {
            $answer->voiceLog()->updateOrCreate([], [
                'status' => 'processing',
                'mimetype' => $mimeType,
                'size' => strlen($audioContent),
                'yandex_id' => $operationId,
            ]);
        }

        return $operationId;
    }

    /**
     * Проверяет готовность распознавания ответа и сохраняет текст.
     */
    public function pollAnswerRecognition(Answer $answer): bool
    {
        $voiceLog = $answer->voiceLog;
        if (!$voiceLog || $voiceLog->status !== 'processing') {
            return false;
        }

        $text = $this->sttService->getResult($voiceLog->yandex_id);

        if ($text) {
            DB::transaction(function () use ($answer, $voiceLog, $text) {
                $answer->update(['text' => $text]);
                $voiceLog->update(['status' => 'completed']);
            });

            return true;
        }

        return false;
    }
}
