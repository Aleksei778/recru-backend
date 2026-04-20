<?php

declare(strict_types=1);

namespace App\Candidate\Repositories;

use App\Candidate\Models\Candidate;

final readonly class CandidateRepository
{
    public function find(int $id): ?Candidate
    {
        return Candidate::find($id);
    }

    public function findByEmail(string $email): ?Candidate
    {
        return Candidate::where('email', $email)->first();
    }

    public function findByPhone(string $phone): ?Candidate
    {
        return Candidate::where('phone', $phone)->first();
    }
}
