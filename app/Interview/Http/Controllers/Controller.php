<?php

declare(strict_types=1);

namespace App\Interview\Http\Controllers;

use App\Candidate\Repositories\Repository as CandidateRepository;
use App\Common\Enum\Locale;
use App\Common\Services\Storage;
use App\Interview\Dto\Create;
use App\Interview\Enum\Decision;
use App\Interview\Http\Requests\CloseRequest;
use App\Interview\Http\Requests\StoreRequest;
use App\Interview\Http\Requests\UpdateQuestionsRequest;
use App\Interview\Http\Resources\Resource;
use App\Interview\Repositories\InterviewRepository;
use App\Interview\Services\Questions\ApproveService;
use App\Vacancy\Repositories\VacancyRepository;
use Carbon\Carbon;
use App\Interview\Models\{Interview, Question};
use App\Interview\Services\{ManageService, CrudService, TokenService};
use Illuminate\Http\{JsonResponse, Request};
use App\Common\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $interviews = $this->interviewRepository->paginateWithCandidateAndVacancy();

        return Resource::collection($interviews);
    }

    public function store(StoreRequest $request): Resource
    {
        $validated = $request->validated();

        $vacancy = $this->vacancyRepository->find($validated['vacancy_id']);
        $candidate = $this->candidateRepository->find($validated['candidate_id']);
        $token = $this->tokenService->generateToken();

        $dto = new Create(
            vacancy: $vacancy,
            candidate: $candidate,
            token: $token,
            tokenExpiresAt: Carbon::parse($validated['token_expires_at']),
            additionalInfo: $validated['additional_info'],
        );

        $interview = $this->createService->create($dto);

        return Resource::make($interview);
    }

    public function nextQuestion(string $token): JsonResponse
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

            return response()->json(['status' => 'completed']);
        }

        return response()->json([
            'question' => $question,
            'audio_url' => $this->storage->url(
                disk: 'yandex_object_storage',
                path: $question->voiceLog->audio_path
            ),
        ]);
    }

    public function show(Interview $interview): Resource
    {
        $interview->load([
            'candidate',
            'vacancy',
            'questions',
            'answers',
        ]);

        return Resource::make($interview);
    }

    public function answer(
        Request $request,
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

    public function updateQuestions(UpdateQuestionsRequest $request, Interview $interview): JsonResponse
    {
        $this->manageService->updateQuestions($request->validated('questions'));

        return response()->json(['status' => 'ok']);
    }

    public function approveQuestions(UpdateQuestionsRequest $request, Interview $interview): JsonResponse
    {
        $locale = Locale::from(config('app.locale', 'ru'));

        $this->approveService->approve(
            interview: $interview,
            questionsNewData: $request->validated('questions'),
            locale: $locale,
            user: $request->user(),
        );

        return response()->json(['status' => 'ok']);
    }

    public function close(CloseRequest $request, Interview $interview): JsonResponse
    {
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
