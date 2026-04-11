<?php

declare(strict_types=1);

namespace App\Interview\Services;

use App\Ai\Yandex\Services\ObjectStorageService;
use App\Interview\Enum\Status;
use App\Interview\Repositories\QuestionRepository;
use App\Interview\Models\{Interview, Question, Answer};
use Psr\Log\LoggerInterface;

final readonly class ManageService
{
    public function __construct(
        private QuestionsService $questionsService,
        private ObjectStorageService $objectStorageService,
        private QuestionRepository $questionRepository,
        private VoiceService $voiceService,
        private EvaluationService $evaluationService,
        private LoggerInterface $logger,
    ) {
    }

    public function start(Interview $interview): void
    {
        if (!$interview->isPending()) {
            return;
        }

        $this->questionsService->generate($interview);
        $interview->markAsInProgress();
    }

    public function getNextQuestion(Interview $interview): ?Question
    {
        return $this->questionRepository->getNextQuestionForInterview($interview);
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

            $stillPending = Answer::whereHas('question', function ($q) use ($interview) {
                    $q->where('interview_id', $interview->id);
                })
                ->whereHas('voiceLog', function ($v) {
                    $v->where('status', 'processing');
                })
                ->exists();

            if (!$stillPending) {
                $interview->update(['status' => Status::COMPLETED]);
                $this->evaluationService->evaluate($interview);
                return true;
            }
        }

        return false;
    }
}
