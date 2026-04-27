<?php

declare(strict_types=1);

namespace App\Skill\Http\Controllers;

use App\Skill\Repositories\Repository;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

final readonly class Controller
{
    public function __construct(
        private Repository $skillRepository,
    ) {
    }

    public function index(Request $request): Collection
    {
        $validated = $request->validate([
            'q' => 'required|string',
        ]);

        return $this->skillRepository->findWithQueryAndLimit($validated['q'], 20);
    }
}
