<?php

declare(strict_types=1);

namespace App\Interview\Repositories;

use App\Interview\Models\Interview;
use App\Interview\Models\Question;

final readonly class QuestionRepository
{
    /**
     * Find multiple questions with answers by interview ID.
     *
     * @param Interview $interview
     * @return Question[]
     */
    public function findManyByInterviewWithAnswers(Interview $interview): array
    {
        return Question::where('interview_id', $interview->id)
            ->with('answer')
            ->get()
            ->toArray();
    }

    public function getNextQuestionForInterview(Interview $interview): ?Question
    {
        return Question::where('interview_id', $interview->id)
            ->whereDoesntHave('answer')
            ->orderBy('number')
            ->first();
    }
}
