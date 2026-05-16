<?php

declare(strict_types=1);

namespace App\Tenant\Http\Controllers;

use App\Tenant\Http\Requests\UpdateRequest;
use App\Tenant\Services\CrudService;
use App\Common\Http\Controllers\{Controller as BaseController};
use Illuminate\Http\JsonResponse;

final readonly class Controller extends BaseController
{
    public function edit(UpdateRequest $request, CrudService $crudService): JsonResponse
    {
        $validated = $request->validated();

        $crudService->update(
            tenant: tenant(),
            name: $validated['name'],
            website: $validated['website'],
            industry: $validated['industry'],
        );

        return response()->json([
            'message' => 'Tenant data has been updated.',
        ]);
    }
}
