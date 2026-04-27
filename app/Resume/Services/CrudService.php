<?php

declare(strict_types=1);

namespace App\Resume\Services;

use App\Resume\Dto\Create;
use App\Resume\Models\Resume;

final readonly class CrudService
{
    public function create(Create $dto): Resume
    {
        return Resume::create($dto->toArray());
    }

    public function updateGrade(
        Resume $resume,
        int $grade,
        string $textGrade,
    ): void {
        $resume->update([
            'grade' => $grade,
            'text_grade' => $textGrade,
        ]);
    }
}
