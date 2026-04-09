<?php

declare(strict_types=1);

namespace App\Email\Http\Controllers;

use App\Base\Enum\Locale;
use App\Base\Http\Controllers\Controller as BaseController;
use App\Email\Http\Requests\SendRequest;
use App\Interview\Services\UrlService;
use App\Email\Services\{SendService, CreateService};
use App\Interview\Repositories\InterviewRepository;
use Illuminate\Http\JsonResponse;

final readonly class Controller extends BaseController
{
    public function __construct(
        public InterviewRepository $interviewRepository,
        public CreateService $createService,
        public SendService $sendService,
        public UrlService $urlService,
    ) {
    }

    public function send(SendRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = $request->user();
        $locale = Locale::from($validated['locale']);
        $interviewId = (int)$validated['interview_id'];

        $interview = $this->interviewRepository->find($interviewId);

        if (!$interview) {
            return response()->json(['message' => 'InterviewMail not found'], status:404);
        }

        $interviewUrl = $this->urlService->getInterviewPageUrl($interview);

        $mailable = $this->sendService->getInterviewMail(
            interview: $interview,
            user: $user,
            interviewUrl: $interviewUrl,
            locale: $locale,
        );

        $this->sendService->sendInterviewMail($mailable);

        $this->createService->create(
            user: $user,
            interview: $interview,
            locale: $locale,
        );

        return response()->json(['message' => 'Email sent successfully']);
    }
}
