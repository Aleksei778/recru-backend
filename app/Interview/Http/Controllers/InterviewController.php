<?php

declare(strict_types=1);

namespace App\Interview\Http\Controllers;

use App\Interview\Http\Resources\InterviewResource;
use App\Interview\Models\Interview;
use App\Interview\Models\Question;
use App\Interview\Services\InterviewManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Base

final readonly class InterviewController extends Controller
{
    public function __construct(
        private InterviewManager $manager
    ) {
    }

    public function create(): InterviewResource
    {
        $interview = $this->manager->();

        return InterviewResource::make($interview);
    }

    /**
     * Старт интервью по токену.
     */
    public function start(string $token): JsonResponse
    {
        $interview = Interview::where('token', $token)->firstOrFail();
        
        $this->manager->startInterview($interview);

        return response()->json([
            'status' => 'started',
            'interview_id' => $interview->id,
        ]);
    }

    /**
     * Получить следующий вопрос.
     */
    public function nextQuestion(Interview $interview): JsonResponse
    {
        $question = $this->manager->getNextQuestion($interview);

        if (!$question) {
            $isCompleted = $this->manager->checkAndComplete($interview);
            
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
