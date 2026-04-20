<?php

declare(strict_types=1);

namespace App\Resume\Services;

use App\Candidate\Enum\{EducationLevel, Status};
use App\Candidate\Services\SocialService;
use App\Candidate\Services\WorkplaceService;
use App\Resume\Models\Resume;
use Illuminate\Support\Facades\DB;

final readonly class SaveResultsService
{
    public function __construct(
        private SocialService $socialService,
        private WorkplaceService $workplaceService,
    ) {
    }

    public function saveEvaluationResult(Resume $resume, string $responseText): bool
    {
        $data = $this->decodeJson($responseText);

        if (!$data) {
            return false;
        }

        $resume->update([
            'summary' => $data['feedback'] ?? null,
            'score' => isset($data['score']) ? (int) $data['score'] : null,
        ]);

        return true;
    }

    public function saveParsedResult(Resume $resume, string $responseText): bool
    {
        $data = $this->decodeJson($responseText);

        if (!$data) {
            return false;
        }

        return DB::transaction(function () use ($resume, $data) {
            $resume->update([
                'parsed_data' => $data,
            ]);

            $candidate = $resume->candidate;
            if ($candidate) {
                // Обновляем данные кандидата из распарсенных данных
                $candidate->update([
                    'first_name' => (string) ($data['first_name'] ?? $candidate->first_name),
                    'last_name' => (string) ($data['last_name'] ?? $candidate->last_name),
                    'middle_name' => $data['middle_name'] ?? $candidate->middle_name,
                    'email' => $data['email'] ?? $candidate->email,
                    'phone' => $data['phone'] ?? $candidate->phone,
                    'experience_years' => isset($data['experience_years']) ? (int) $data['experience_years'] : $candidate->experience_years,
                    'education_level' => isset($data['education_level'])
                        ? EducationLevel::tryFrom($data['education_level']) ?? $candidate->education_level
                        : $candidate->education_level,
                    'status' => Status::NEW, // Переводим из PROCESSING в NEW
                ]);

                $this->socialService->syncSocials($candidate, $data['socials'] ?? []);
                $this->workplaceService->syncWorkPlaces($candidate, $data['work_places'] ?? []);
            }

            return true;
        });
    }

    private function decodeJson(string $text): ?array
    {
        $text = trim($text);

        // Попытка извлечь JSON если он обернут в markdown блоки
        if (preg_match('/^```json\s+(.*?)\s+```$/s', $text, $matches)) {
            $text = $matches[1];
        }

        try {
            return json_decode($text, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }
    }
}
