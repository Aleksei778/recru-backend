<?php

declare(strict_types=1);

namespace App\Interview\Services;

use App\Ai\Operation\Dto\Create as OperationCreateDto;
use App\Ai\Operation\Enum\Status as OperationStatus;
use App\Ai\Operation\Enum\Type as OperationType;
use App\Ai\Operation\Services\CrudService as OperationCrudService;
use App\Ai\Stt\Job\ProcessSttJob;
use App\Common\Services\Storage;
use App\Interview\Repositories\QuestionRepository;
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
        $storageProvider = config('app.storage_provider');

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

        $interview->questions->each(function (Question $question) {
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

            ProcessSttJob::dispatch($operation)->delay(now()->addSeconds(5));
        });
    }
}
