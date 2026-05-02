<?php

declare(strict_types=1);

namespace App\Interview\Repositories;

use App\Interview\Models\Interview;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class InterviewRepository
{
    public function find(int $id): ?Interview
    {
        return Interview::find($id);
    }

    public function findByToken(string $token): ?Interview
    {
        return Interview::where('token', $token)->first();
    }

    public function paginateWithCandidateAndVacancy(): LengthAwarePaginator
    {
        return Interview::with(['candidate', 'vacancy', 'questions'])
            ->latest()
            ->paginate();
    }
}
