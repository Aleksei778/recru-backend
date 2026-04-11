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
use App\Base\Http\Controllers\Controller as BaseController;
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

    public function show(Interview $interview): Resource
    {
        $interview->load(['candidate', 'vacancy']);

        return Resource::make($interview);
    }

    public function start(string $token): JsonResponse
    {
        $interview = $this->interviewRepository->findByToken($token);

        if (!$interview) {
            return response()->json(['error' => 'Invalid token for interview'], 404);
        }

        $this->manageService->start($interview);

        return response()->json(['message' => 'Interview started successfully']);
    }

    public function nextQuestion(Interview $interview): JsonResponse
    {
        $question = $this->manageService->getNextQuestion($interview);

        if (!$question) {
            $isCompleted = $this->manageService->checkAndComplete($interview);
            
            return response()->json([
                'status' => $isCompleted ? 'completed' : 'processing_results',
                'grade' => $interview->grade,
                'feedback' => $interview->text_grade,
            ]);
        }

        $audioUrl = $this->manager->getQuestionAudio($question);

        return response()->json([
            'question_id' => $question->id,
            'number' => $question->number,
            'text' => $question->text,
            'audio_url' => $audioUrl,
        ]);
    }

    /**
     * Отправить аудио-ответ.
     */
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
