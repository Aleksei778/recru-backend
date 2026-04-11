<?php

declare(strict_types=1);

namespace App\Candidate\Services;

use App\Candidate\Dto\{CreateDto, UpdateDto};
use App\Candidate\Enum\Status;
use App\Candidate\Models\Candidate;

final readonly class CrudService
{
    public function create(CreateDto $dto): Candidate
    {
        $data = $dto->toArray();
        if (!isset($data['status'])) {
            $data['status'] = Status::NEW;
        }

        return Candidate::create($data);
    }

    public function update(Candidate $candidate, UpdateDto $dto): Candidate
    {
        $candidate->update($dto->toArray());

        return $candidate;
    }

    public function delete(Candidate $candidate): bool
    {
        return (bool) $candidate->delete();
    }
}
