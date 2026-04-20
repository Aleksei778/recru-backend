<?php

declare(strict_types=1);

namespace App\Interview\Services\Answers;

use App\Interview\Models\{Answer, Question};

final readonly class CrudService
{
    public function create(Question $question): Answer
    {
        $answer = new Answer([
            'question_id' => $question->id,
        ]);

        $answer->save();

        return $answer;
    }

    public function update(Answer $answer, string $text): void
    {
        $answer->update([
            'text' => $text,
        ]);
    }
}
