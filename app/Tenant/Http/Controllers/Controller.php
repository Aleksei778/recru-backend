<?php

declare(strict_types=1);

namespace App\Tenant\Http\Controllers;

use App\Tenant\Http\Requests\UpdateRequest;
use App\Tenant\Http\Resources\Resource;
use App\Tenant\Services\CrudService;
use App\Common\Http\Controllers\{Controller as BaseController};

final readonly class Controller extends BaseController
{
    public function edit(UpdateRequest $request, CrudService $crudService): Resource
    {
        $validated = $request->validated();

        $crudService->update(
            tenant: tenant(),
            name: $validated['name'],
            website: $validated['website'],
            industry: $validated['industry'],
        );

        return Resource::make(tenant());
    }
}
