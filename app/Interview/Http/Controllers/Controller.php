<?php

declare(strict_types=1);

namespace App\Interview\Http\Controllers;

use App\Vacancy\Repositories\VacancyRepository;
use App\Candidate\Repositories\Repository as CandidateRepository;
use App\Common\{
    Services\Storage,
    Http\Controllers\Controller as BaseController
};
use App\Interview\{
    Dto\Create,
    Enum\Decision,
    Http\Requests\CloseRequest,
    Http\Requests\StoreRequest,
    Http\Requests\UpdateQuestionsRequest,
    Http\Resources\Resource,
    Repositories\InterviewRepository,
    Services\Questions\ApproveService,
    Services\Questions\GenerateService,
    Services\ManageService,
    Services\CrudService,
    Services\TokenService,
    Models\Interview,
    Models\Question,
};
use Carbon\Carbon;
use Illuminate\Http\{
    JsonResponse,
    Request,
    Resources\Json\AnonymousResourceCollection
};

final readonly class Controller extends BaseController
{
    public function __construct(
        private ManageService $manageService,
        private CrudService $createService,
        private InterviewRepository $interviewRepository,
        private VacancyRepository  $vacancyRepository,
        private CandidateRepository $candidateRepository,
        private TokenService $tokenService,
        private Storage $storage,
        private ApproveService $approveService,
        private GenerateService $generateService,
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $interviews = $this->interviewRepository->paginateWithCandidateAndVacancy();

        return Resource::collection($interviews);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $vacancy = $this->vacancyRepository->find($validated['vacancy_id']);
        $candidate = $this->candidateRepository->find($validated['candidate_id']);
        $token = $this->tokenService->generateToken();

        $interview = $this->createService->create(new Create(
            vacancy: $vacancy,
            candidate: $candidate,
            questionsNumber: $validated['questions_number'],
            token: $token,
            tokenExpiresAt: Carbon::now()->addDays(7),
        ));

        $this->generateService->generate($interview);

        return new JsonResponse([
            'interview' => Resource::make($interview->load('questions')),
            'token' => $token,
            'link' => $this->tokenService->getInterviewPageUrl($interview),
        ]);
    }

    public function nextQuestion(string $subdomain, string $token): JsonResponse
    {
        $interview = $this->interviewRepository->findByToken($token);

        if (!$interview) {
            return response()->json(['error' => 'Interview not found'], 404);
        }

        if ($interview->isReady()) {
            $interview->markAsInProgress();
        }

        if (!$interview->isInProgress()) {
            return response()->json(['error' => 'Interview not available'], 422);
        }

        $question = $this->manageService->getNextQuestion($interview);

        if (!$question) {
            $this->manageService->complete($interview);

            return response()->json([
                'is_completed' => true,
                'question' => null,
                'audio_url' => null
            ]);
        }

        return response()->json([
            'is_completed' => false,
            'question' => $question,
            'audio_url' => $this->storage->url(
                disk: config('filesystems.default'),
                path: $question->voiceLog->audio_path
            ),
            'total_questions' => $interview->questions_number,
        ]);
    }

    public function show(string $subdomain, Interview $interview): Resource
    {
        $interview->load([
            'candidate',
            'vacancy',
            'questions',
        ]);

        return Resource::make($interview);
    }

    public function answer(
        Request $request,
        string $subdomain,
        string $token,
        Question $question
    ): JsonResponse {
        $interview = $this->interviewRepository->findByToken($token);

        if (!$interview?->isInProgress()) {
            return response()->json(['error' => 'Interview not in progress'], 422);
        }

        $audio = $request->file('audio');

        if (!$audio) {
            return response()->json(['error' => 'No audio provided'], 400);
        }

        $this->manageService->submitAnswer($question, $audio);

        return response()->json(['status' => 'ok']);
    }

    public function updateQuestions(
        UpdateQuestionsRequest $request,
        string $subdomain,
        Interview $interview
    ): JsonResponse {
        $this->manageService->updateQuestions($request->validated('questions'));

        return response()->json(['status' => 'ok']);
    }

    public function approveQuestions(
        UpdateQuestionsRequest $request,
        string $subdomain,
        Interview $interview
    ): JsonResponse {
        $this->approveService->approve(
            interview: $interview,
            questionsNewData: $request->validated('questions'),
        );

        return response()->json(['status' => 'ok']);
    }

    public function close(
        CloseRequest $request,
        string $subdomain,
        Interview $interview
    ): JsonResponse {
        $decision = Decision::from($request->validated('decision'));

        $this->manageService->close($interview, $decision);

        return response()->json(['status' => 'ok']);
    }

    public function regenerateToken(Interview $interview): JsonResponse
    {
        $token = $this->tokenService->generateToken();

        $interview->update([
            'token' => $token,
        ]);

        return response()->json(['token' => $token]);
    }
}
