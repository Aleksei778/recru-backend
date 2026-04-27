<?php

declare(strict_types=1);

namespace App\Email\Http\Controllers;

use App\Common\Enum\Locale;
use App\Common\Http\Controllers\Controller as BaseController;
use App\Email\Http\Requests\SendRequest;
use App\Interview\Services\TokenService;
use App\Email\Services\{SendService, CrudService};
use App\Interview\Repositories\InterviewRepository;
use Illuminate\Http\JsonResponse;

final readonly class Controller extends BaseController
{
    public function __construct(
        public InterviewRepository $interviewRepository,
        public CrudService $createService,
        public SendService $sendService,
        public TokenService $tokenService,
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
            return response()->json(['message' => 'InterviewInvitationMail not found'], status:404);
        }

        $interviewUrl = $this->tokenService->getInterviewPageUrl($interview);

        $mailable = $this->sendService->getInterviewInvitationMail(
            interview: $interview,
            user: $user,
            interviewUrl: $interviewUrl,
            locale: $locale,
        );

        $this->sendService->sendInterviewInvitationMail($mailable);

        $this->createService->create(
            user: $user,
            interview: $interview,
            locale: $locale,
        );

        return response()->json(['message' => 'Email sent successfully']);
    }

    public
}
