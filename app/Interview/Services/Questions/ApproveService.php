<?php

declare(strict_types=1);

namespace App\Interview\Services\Questions;

use App\Common\Enum\Locale;
use App\Email\Jobs\NotifyUserQuestionsReadyJob;
use App\Interview\Jobs\SynthesizeQuestionJob;
use App\Interview\Models\Interview;
use App\Interview\Repositories\QuestionRepository;
use App\User\Models\User;
use Illuminate\Support\Facades\Bus;

final readonly class ApproveService
{
    public function __construct(
        private CrudService $crudService,
        private QuestionRepository $questionRepository,
    ) {
    }

    public function approve(
        Interview $interview,
        array $questionsNewData,
        Locale $locale,
        User $user,
    ): void {
        if (!$interview->isQuestionsReview()) {
            return;
        }

        foreach ($questionsNewData as $questionData) {
            $question = $this->questionRepository->find($questionData['id']);

            $this->crudService->update(
                question: $question,
                text: $questionData['text'],
                number: $questionData['number'],
            );
        }

        $questions = $interview->questions()->orderBy('number')->get();

        $chain = $questions
            ->map(fn($q) => new SynthesizeQuestionJob($q))
            ->toArray();

        Bus::chain([
            ...$chain,
            new NotifyUserQuestionsReadyJob(
                interview: $interview,
                hr: $user,
            ),
        ])->dispatch();

        $interview->markAsSynthesizing();
    }
}
