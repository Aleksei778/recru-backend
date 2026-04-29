<?php

declare(strict_types=1);

namespace App\Resume\Http\Controllers;

use App\Common\Http\Controllers\Controller as BaseController;
use App\Resume\Http\Requests\StringRequest;
use App\Resume\Services\FileUploadService;
use App\Resume\Http\Requests\FileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
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

    public function parseString(StringRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $string = $validated['resume'];

        $tmpPath = tempnam(sys_get_temp_dir(), 'resume_') . '.txt';
        file_put_contents($tmpPath, $string);

        $file = new UploadedFile(
            $tmpPath,
            originalName: 'resume.txt',
            mimeType: 'text/plain',
            error: null,
            test: true
        );

        $result = $this->fileUploadService->handleFileUpload(
            $file,
            Auth::user()->tenant_id,
            Auth::id()
        );

        return new JsonResponse($result);
    }
}
