<?php

declare(strict_types=1);

namespace App\Email\Http\Controllers;

use App\Common\Http\Controllers\Controller as BaseController;
use App\Email\Http\Requests\SendRequest;
use App\Email\Http\Resources\Collection;
use App\Email\Http\Resources\Resource;
use App\Email\Models\Email;
use App\Email\Repositories\Repository as EmailRepository;
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
        public EmailRepository $emailRepository,
    ) {
    }

    public function indexInbox(): Collection
    {
        return Collection::make(
            $this->emailRepository->findInboxWithPaginate()
        );
    }

    public function show(Email $email): Resource
    {
        return Resource::make($email);
    }

    public function indexSent(): Collection
    {
        return Collection::make(
            $this->emailRepository->findSentWithPaginate()
        );
    }

    public function sendInvitation(SendRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = $request->user();
        $interviewId = (int)$validated['interview_id'];

        $interview = $this->interviewRepository->find($interviewId);

        if (!$interview) {
            return response()->json(['message' => 'interview not found'], 404);
        }

        $interviewUrl = $this->tokenService->getInterviewPageUrl($interview);

        $mailable = $this->sendService->getInterviewInvitationMail(
            interview: $interview,
            user: $user,
            interviewUrl: $interviewUrl,
        );

        $this->sendService->sendInterviewInvitationMail($mailable);

        $this->createService->createInvitation($user, $interview);

        return response()->json(['message' => 'Email sent successfully']);
    }
}
