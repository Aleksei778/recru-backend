<?php

declare(strict_types=1);

namespace App\Interview\Repositories;

use App\Interview\Models\Interview;
use App\Interview\Models\Question;
use Illuminate\Support\Collection;

final readonly class QuestionRepository
{
    /**
     * Find multiple questions with answers by interview ID.
     *
     * @param Interview $interview
     * @return Collection<Question>
     */
    public function findManyByInterviewWithAnswers(Interview $interview): Collection
    {
        return Question::where('interview_id', $interview->id)
            ->with('answer')
            ->get();
    }

    public function find(int $id): ?Question
    {
        return Question::find($id);
    }

    public function getNextQuestionForInterview(Interview $interview): ?Question
    {
        return Question::where('interview_id', $interview->id)
            ->whereDoesntHave('answer')
            ->orderBy('number')
            ->first();
    }
}
