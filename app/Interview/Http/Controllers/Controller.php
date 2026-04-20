<?php

declare(strict_types=1);

namespace App\Interview\Http\Controllers;

use App\Candidate\Repositories\CandidateRepository;
use App\Interview\Dto\Create;
use App\Interview\Http\Requests\StoreRequest;
use App\Interview\Http\Resources\Resource;
use App\Interview\Repositories\InterviewRepository;
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
        private VacancyRepository $vacancyRepository,
        private CandidateRepository $candidateRepository,
        private TokenService $tokenService,
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

        }
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

    public function answer(Request $request, Question $question): JsonResponse
    {
        $audioFile = $request->file('audio');
        
        if (!$audioFile) {
            return response()->json(['error' => 'No audio file provided'], 400);
        }

        $operationId = $this->manager->submitAnswer(
            $question,
            file_get_contents($audioFile->getRealPath()),
            $audioFile->getMimeType()
        );

        return response()->json([
            'status' => 'submitted',
            'operation_id' => $operationId,
        ]);
    }
}
