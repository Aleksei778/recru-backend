<?php

declare(strict_types=1);

namespace App\Resume\Services;

use App\Ai\Gpt\Contracts\{AsyncInterface as GptInterface};
use App\Ai\Gpt\Prompts\Resume\ParseGenerator;
use App\Ai\Operation\Dto\{Create as OperationCreateDto};
use App\Ai\Operation\Enum\{Status, Type};
use App\Ai\Operation\Jobs\CheckOperationJob;
use App\Ai\Operation\Services\{CrudService as OperationCrudService};
use App\Candidate\Enum\EducationLevel;
use App\Candidate\Services\{App\Candidate\Services\Social\CrudService,
    App\Candidate\Services\Workplace\WorkplaceService};
use App\Common\Services\JsonDecoder;
use App\Resume\Models\Resume;
use Illuminate\Support\Facades\DB;

final readonly class ParseService
{
    public function __construct(
        private GptInterface                                  $gptService,
        private ParseGenerator                                $parseGenerator,
        private \App\Candidate\Services\Social\CrudService    $socialService,
        private \App\Candidate\Services\Workplace\CrudService $workplaceService,
        private JsonDecoder                                   $jsonDecoder,
        private OperationCrudService                          $operationCrudService,
    ) {
    }

    public function parse(string $text, Resume $resume): string
    {
        $providerId = $this->gptService->completionAsync(
            messages: $this->parseGenerator->messages($text)
        );

        $operation = $this->operationCrudService->create(new OperationCreateDto(
            type: Type::ResumeParsingGpt,
            subjectId: $resume->id,
            subjectType: Resume::class,
            provider: config('ai.provider'),
            providerId: $providerId,
            status: Status::Pending,
        ));

        CheckOperationJob::dispatch($operation->id)->delay(now()->addSeconds(3));

        return $operation->id;
    }

    public function handleParsedResult(Resume $resume, string $responseText): bool
    {
        $data = $this->jsonDecoder->decodeJson($responseText);

        if (!$data) {
            return false;
        }

        return DB::transaction(function () use ($resume, $data) {
            $resume->update([
                'parsed_data' => $data,
            ]);

            $candidate = $resume->candidate;
            if ($candidate) {
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
                ]);

                $this->socialService->syncSocials($candidate, $data['socials'] ?? []);
                $this->workplaceService->syncWorkPlaces($candidate, $data['work_places'] ?? []);
            }

            return true;
        });
    }
}
