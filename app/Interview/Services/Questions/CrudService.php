<?php

declare(strict_types=1);

namespace App\Interview\Services\Questions;

use App\Interview\Models\{Interview, Question};

class CrudService
{
    public function create(
        Interview $interview,
        string $text,
        int $number
    ): Question {
        $question = new Question([
            'interview_id' => $interview->id,
            'text' => $text,
            'number' => $number,
        ]);

        $question->save();

        return $question;
    }

    public function update(Question $question, string $text, int $number): void
    {
        $question->update([
            'text' => $text,
            'number' => $number,
        ]);
    }
}
