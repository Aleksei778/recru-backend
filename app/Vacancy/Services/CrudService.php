<?php

declare(strict_types=1);

namespace App\Vacancy\Services;

use App\Vacancy\Enum\Status;
use App\Vacancy\Models\Vacancy;
use Illuminate\Support\Facades\Auth;

final readonly class CrudService
{
    public function create(array $data): Vacancy
    {
        return Vacancy::create([
            ...$data,
            'tenant_id' => Auth::user()?->tenant_id,
            'created_by_id' => Auth::id(),
            'status' => $data['status'] ?? Status::DRAFT,
            'published_at' => ($data['status'] ?? null) === Status::PUBLISHED->value ? now() : null,
        ]);
    }

    public function update(Vacancy $vacancy, array $data): Vacancy
    {
        if (isset($data['status']) && $data['status'] === Status::PUBLISHED->value && $vacancy->status !== Status::PUBLISHED) {
            $data['published_at'] = now();
        }

        if (isset($data['status']) && $data['status'] === Status::CLOSED->value && $vacancy->status !== Status::CLOSED) {
            $data['closed_at'] = now();
        }

        $vacancy->update($data);

        return $vacancy;
    }

    public function delete(Vacancy $vacancy): bool
    {
        return $vacancy->delete();
    }
}
