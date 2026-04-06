<?php

declare(strict_types=1);

namespace App\Interview\Services;

use App\Ai\Yandex\Services\ObjectStorageService;
use App\Interview\Enum\Status;
use App\Interview\Models\Interview;
use App\Interview\Models\Question;
use App\Interview\Models\Answer;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface;

final readonly class InterviewManager
{
    public function __construct(
        private InterviewService $interviewService,
        private InterviewVoiceService $voiceService,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Запуск интервью: генерация вопросов и установка статуса.
     */
    public function startInterview(Interview $interview): void
    {
        if ($interview->status !== Status::PENDING) {
            return;
        }

        $this->interviewService->generateQuestions($interview);
        $interview->update(['status' => Status::IN_PROGRESS]);
    }

    /**
     * Получить текущий вопрос интервью (первый без ответа).
     */
    public function getNextQuestion(Interview $interview): ?Question
    {
        return Question::where('interview_id', $interview->id)
            ->whereDoesntHave('answer')
            ->orderBy('number')
            ->first();
    }

    /**
     * Получить аудио-ссылку для вопроса (с генерацией если нужно).
     */
    public function getQuestionAudio(Question $question): ?string
    {
        $voiceLog = $question->voiceLog;
        
        if ($voiceLog && $voiceLog->status === 'completed') {
            // В идеале тут нужен доступ к S3 для получения ссылки, 
            // но в нашей упрощенной модели мы пересоздаем пресайнд ссылку
            return app(ObjectStorageService::class)->getObjectUri($voiceLog->yandex_id);
        }

        return $this->voiceService->synthesizeQuestion($question);
    }

    /**
     * Обработка ответа кандидата.
     */
    public function submitAnswer(Question $question, string $audioContent, string $mimeType): string
    {
        $operationId = $this->voiceService->handleAnswerAudio($question, $audioContent, $mimeType);
        
        // Если это был последний вопрос, можно запустить проверку завершения асинхронно
        return $operationId;
    }

    /**
     * Проверка завершения интервью и запуск оценки.
     */
    public function checkAndComplete(Interview $interview): bool
    {
        $totalQuestions = Question::where('interview_id', $interview->id)->count();
        $answeredQuestions = Answer::whereHas('question', function ($q) use ($interview) {
            $q->where('interview_id', $interview->id);
        })->count();

        if ($totalQuestions > 0 && $totalQuestions === $answeredQuestions) {
            // Все ответы получены, но нужно дождаться окончания STT для всех ответов
            $pendingStt = Answer::whereHas('question', function ($q) use ($interview) {
                    $q->where('interview_id', $interview->id);
                })
                ->whereHas('voiceLog', function ($v) {
                    $v->where('status', 'processing');
                })
                ->get();

            foreach ($pendingStt as $answer) {
                $this->voiceService->pollAnswerRecognition($answer);
            }

            // Перепроверяем еще раз после поллинга
            $stillPending = Answer::whereHas('question', function ($q) use ($interview) {
                    $q->where('interview_id', $interview->id);
                })
                ->whereHas('voiceLog', function ($v) {
                    $v->where('status', 'processing');
                })
                ->exists();

            if (!$stillPending) {
                $interview->update(['status' => Status::COMPLETED]);
                $this->interviewService->evaluateInterview($interview);
                return true;
            }
        }

        return false;
    }
}
