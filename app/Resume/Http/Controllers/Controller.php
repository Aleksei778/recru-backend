<?php

declare(strict_types=1);

namespace App\Resume\Http\Controllers;

use App\Candidate\{
    Http\Resources\Resource as CandidateResource,
    Repositories\Repository as CandidateRepository,
};
use App\Common\Http\Controllers\Controller as BaseController;
use App\Resume\{
    Http\Requests\SaveResumeRequest,
    Http\Requests\StringRequest,
    Http\Requests\FileRequest,
    Services\AttachToCandidateService,
    Services\FileUploadService,
    Repositories\Repository as ResumeRepository,
};
use Illuminate\Http\{
    JsonResponse,
    UploadedFile
};

final readonly class Controller extends BaseController
{
    public function __construct(
        private FileUploadService $fileUploadService,
        private AttachToCandidateService $attachToCandidateService,
        private CandidateRepository $candidateRepository,
        private ResumeRepository $resumeRepository,
    ) {
    }

    public function parseFile(FileRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $result = $this->fileUploadService->handleFileUpload($validated['resume']);

        return new JsonResponse($result);
    }

    public function parseString(StringRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $tmpPath = tempnam(sys_get_temp_dir(), 'resume_') . '.txt';
        file_put_contents($tmpPath, $validated['resume']);

        $file = new UploadedFile(
            $tmpPath,
            originalName: 'resume.txt',
            mimeType: 'text/plain',
            error: null,
            test: true
        );

        $result = $this->fileUploadService->handleFileUpload($file);

        return new JsonResponse($result);
    }

    public function save(SaveResumeRequest $request): CandidateResource|JsonResponse
    {
        $validated = $request->validated();

        $resume = $this->resumeRepository->find($validated['resume_id']);

        if (!$resume) {
            return response()->json(['error' => 'Resume not found.'], 404);
        }

        if ($validated['mode'] === 'new') {
            $candidate = $this->attachToCandidateService->createCandidateFromResume($resume);
        } else {
            $this->attachToCandidateService->attachToCandidate(
                $resume,
                $validated['candidate_id']
            );

            $candidate = $this->candidateRepository->find($validated['candidate_id']);
        }

        return CandidateResource::make($candidate);
    }
}
