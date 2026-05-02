<?php

declare(strict_types=1);

namespace App\Resume\Services;

use App\Ai\Gpt\{
    Providers\GptInterface,
    Prompts\Resume\ParseGenerator
};
use App\Ai\Operation\{
    Dto\Create as OperationCreateDto,
    Enum\Status,
    Enum\Type,
    Jobs\CheckOperationJob,
    Services\CrudService as OperationCrudService,

};
use App\Common\Services\JsonDecoder;
use App\Resume\Models\Resume;

final readonly class ParseService
{
    public function __construct(
        private GptInterface $gptService,
        private ParseGenerator $parseGenerator,
        private JsonDecoder $jsonDecoder,
        private OperationCrudService $operationCrudService,
    ) {
    }

    public function parse(string $text, Resume $resume): int
    {
        $providerId = $this->gptService->completion(
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

        $resume->update([
            'parsed_data' => $data,
        ]);

        return true;
    }
}
