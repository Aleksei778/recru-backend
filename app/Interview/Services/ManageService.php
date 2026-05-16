<?php

declare(strict_types=1);

namespace App\Interview\Services;

use App\Ai\Operation\Dto\Create as OperationCreateDto;
use App\Ai\Operation\Enum\Status as OperationStatus;
use App\Ai\Operation\Enum\Type as OperationType;
use App\Ai\Operation\Services\CrudService as OperationCrudService;
use App\Ai\Stt\Job\ProcessSttJob;
use App\Common\Enum\Locale;
use App\Common\Services\Storage;
use App\Email\Jobs\NotifyCandidateDecisionJob;
use App\Email\Jobs\NotifyUserInterviewFinishedJob;
use App\Interview\Enum\Decision;
use App\Interview\Repositories\QuestionRepository;
use App\Interview\Services\Questions\CrudService as QuestionsCrudService;
use App\VoiceLog\Dto\Create as VoiceLogCreateDto;
use App\VoiceLog\Enum\Type as VoiceLogType;
use App\Interview\Services\Answers\{StoragePathHelper, CrudService as AnswersCrudService};
use Illuminate\Http\UploadedFile;
use App\Interview\Models\{Answer, Interview, Question};
use App\VoiceLog\Services\CrudService as VoiceLogCrudService;

final readonly class ManageService
{
    public function __construct(
        private QuestionRepository $questionRepository,
        private QuestionsCrudService $questionsCrudService,
        private Storage $storage,
        private AnswersCrudService $answersCrudService,
        private VoiceLogCrudService $voiceLogCrudService,
        private OperationCrudService $operationCrudService,
    ) {
    }

    public function getNextQuestion(Interview $interview): ?Question
    {
        return $this->questionRepository->getNextQuestionForInterview($interview);
    }

    public function submitAnswer(Question $question, UploadedFile $audio): void
    {
        $storageProvider = config('filesystems.default');

        $path = StoragePathHelper::getStoragePath($question);
        $this->storage->put($storageProvider, $path, $audio->getContent());

        $answer = $this->answersCrudService->create($question);

        $this->voiceLogCrudService->create(new VoiceLogCreateDto(
            subjectId: $answer->id,
            subjectType: Answer::class,
            audioPath: $path,
            mimeType: $audio->getMimeType(),
            type: VoiceLogType::Stt
        ));
    }

    public function complete(Interview $interview): void
    {
        $interview->markAsProcessing();

        $hr = $interview->vacancy->createdBy;
        $candidate = $interview->candidate;

        $hrLocale = $hr->locale ?? Locale::RU;
        $candidateLocale = $candidate->locale ?? Locale::RU;

        NotifyUserInterviewFinishedJob::dispatch($interview, $hr, $hrLocale);

        $interview->questions->each(function (Question $question) use ($candidateLocale): void {
            $answer = $question->answer;

            if (!$answer) {
                return;
            }

            $operationDto = new OperationCreateDto(
                type: OperationType::InterviewAnswersStt,
                subjectId: $answer->id,
                subjectType: Answer::class,
                provider: config('ai.provider'),
                providerId: '',
                status: OperationStatus::Pending,
            );

            $operation = $this->operationCrudService->create($operationDto);

            ProcessSttJob::dispatch($operation, $candidateLocale)->delay(now()->addSeconds(5));
        });
    }

    public function updateQuestions(array $questionsData): void
    {
        foreach ($questionsData as $questionData) {
            $question = $this->questionRepository->find($questionData['id']);

            if (!$question) {
                continue;
            }

            $this->questionsCrudService->update($question, $questionData['text'], $questionData['number']);
        }
    }

    public function close(Interview $interview, Decision $decision): void
    {
        $interview->markAsClosed();

        $locale = $interview->candidate->locale ?? Locale::RU;

        NotifyCandidateDecisionJob::dispatch($interview, $decision, $locale);
    }
}
