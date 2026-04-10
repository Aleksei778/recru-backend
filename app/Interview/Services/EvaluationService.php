<?php

declare(strict_types=1);

namespace App\Interview\Services;

use App\Ai\Yandex\Services\Gpt\GptService;
use App\Interview\Models\Interview;
use App\Ai\Prompts\Services\EvaluationGenerator;
use Psr\Log\LoggerInterface;

final readonly class EvaluationService
{
    public function __construct(
        private EvaluationGenerator $evaluationGenerator,
        private GptService $gptService,
        private LoggerInterface $logger,
    ) {
    }

    public function evaluate(Interview $interview): bool
    {
        $response = $this->gptService->completion(
            messages: $this->evaluationGenerator->messages($interview)
        );

        if (!$response) {
            $this->logger->error('Failed to evaluate interview', [
                'interview_id' => $interview->id,
            ]);

            return false;
        }

        $result = $this->jsonDecode(
            json: $this->markdownClean($response),
        );

        if (!$result) {
            $this->logger->error('No result from evaluation after json decode', [
                'interview_id' => $interview->id,
            ]);

            return false;
        }

        $interview->update([
            'grade' => $result['grade'] ?? 0,
            'text_grade' => $result['feedback'] ?? '',
        ]);

        return true;
    }

    private function markdownClean(string $response): string
    {
        return preg_replace(
            pattern: '/```json|```/',
            replacement: '',
            subject: $response
        );
    }

    private function jsonDecode(string $json): array
    {
        return json_decode($json, associative: true);
    }
}
