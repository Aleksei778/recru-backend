<?php

declare(strict_types=1);

namespace App\Resume\Http\Controllers;

use App\Common\Http\Controllers\Controller as BaseController;
use App\Resume\Services\FileUploadService;
use App\Resume\Http\Requests\FileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final readonly class Controller extends BaseController
{
    public function __construct(
        private FileUploadService $fileUploadService,
    ) {
    }

    public function parseFile(FileRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $file = $validated['resume'];

        $result = $this->fileUploadService->handleFileUpload(
            $file,
            Auth::user()->tenant_id,
            Auth::id()
        );

        return new JsonResponse($result);
    }
}
