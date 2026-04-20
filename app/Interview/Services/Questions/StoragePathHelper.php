<?php

declare(strict_types=1);

namespace App\Interview\Services\Questions;

use App\Interview\Models\Question;

final readonly class StoragePathHelper
{
    public static function getStoragePath(Question $question): string
    {
        return 'interviews/' .
            $question->interview->id . '/' .
            'questions/' .
            $question->id . '/' .
            'question.ogg';
    }
}
