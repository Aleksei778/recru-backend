<?php

declare(strict_types=1);

namespace App\Interview\Services;

use App\Ai\Yandex\Dto\Gpt\Message;
use App\Ai\Yandex\Services\Gpt\GptService;
use App\Interview\Models\Interview;
use App\Interview\Models\Question;
use App\Interview\Models\Answer;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

final readonly class InterviewService
{
    public function __construct(
        private GptService $gptService,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Генерирует 10 вопросов для интервью на основе вакансии.
     */
    public function generateQuestions(Interview $interview): void
    {
        $vacancy = $interview->vacancy;
        $prompt = "Ты — профессиональный IT-рекрутер. Твоя задача — составить 10 вопросов для первичного интервью кандидата на вакансию: '{$vacancy->title}'.
        Описание вакансии: {$vacancy->description}.
        Вопросы должны проверять как hard skills, так и soft skills. 
        Выдай только список вопросов, каждый вопрос с новой строки, без номеров и лишнего текста.";

        $messages = [
            new Message('system', 'Ты помощник рекрутера, который составляет вопросы для интервью.'),
            new Message('user', $prompt),
        ];

        $response = $this->gptService->completion($messages);

        if (!$response) {
            $this->logger->error('Failed to generate questions for interview', ['interview_id' => $interview->id]);
            return;
        }

        $questions = array_filter(explode("\n", $response));
        
        DB::transaction(function () use ($interview, $questions) {
            foreach (array_slice($questions, 0, 10) as $index => $text) {
                Question::create([
                    'interview_id' => $interview->id,
                    'text' => trim($text),
                    'number' => $index + 1,
                ]);
            }
        });
    }

    /**
     * Оценивает результаты интервью.
     */
    public function evaluateInterview(Interview $interview): void
    {
        $questionsAndAnswers = Question::where('interview_id', $interview->id)
            ->with('answer')
            ->get();

        $content = "Оцени ответы кандидата на интервью для вакансии '{$interview->vacancy->title}'.\n\n";

        foreach ($questionsAndAnswers as $qa) {
            $answerText = $qa->answer ? $qa->answer->text : 'Нет ответа';
            $content .= "Вопрос: {$qa->text}\nОтвет: {$answerText}\n\n";
        }

        $content .= "Дай оценку кандидату по 10-балльной шкале и краткий текстовый фидбек. 
        Формат ответа JSON: {\"grade\": 8, \"feedback\": \"Текст фидбека\"}";

        $messages = [
            new Message('system', 'Ты эксперт по найму в IT. Оценивай ответы технически грамотно.'),
            new Message('user', $content),
        ];

        $response = $this->gptService->completion($messages);

        if ($response) {
            // Очистка от markdown если есть
            $json = preg_replace('/```json|```/', '', $response);
            $result = json_decode(trim($json), true);

            if ($result) {
                $interview->update([
                    'grade' => $result['grade'] ?? 0,
                    'text_grade' => $result['feedback'] ?? '',
                ]);
            }
        }
    }
}
