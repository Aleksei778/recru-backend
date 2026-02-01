<?php

declare(strict_types=1);

namespace App\Yandex\Services;

use App\DTO\Yandex\Operation\OperationCreate;
use App\DTO\Yandex\Requests\GptRequest;
use App\Services\Yandex\IamTokenService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final readonly class GptService
{
    private string $gptUrl;
    private string $modelUri;
    private float $temperature;
    private int $maxTokens;
    private bool $stream;
    private string $systemPrompt;

    public function __construct(
        private IamTokenService $iamTokenService,
    ) {
        $this->gptUrl = 'https://llm.api.cloud.yandex.net/foundationModels/v1/completionAsync';
        $this->modelUri = sprintf(
            'gpt://%s/%s',
            config('services.yandex.folder_id'),
            'yandexgpt-lite'
        );
        $this->temperature = 0.6;
        $this->maxTokens = 2000;
        $this->stream = false;
        $this->systemPrompt = config('services.yandex.system_prompt');
    }

    public function sendMessageToGpt(GptRequest $gptRequest): ?OperationCreate
    {
        try {
            $iamToken = $this->iamTokenService->getIamToken();

            $categories = implode(', ', CategoryCode::getCategories());

            $payload = [
                'modelUri' => $this->modelUri,
                'completionOptions' => [
                    'stream' => $this->stream,
                    'temperature' => $this->temperature,
                    'maxTokens' => $this->maxTokens,
                ],
                'messages' => [
                    ['role' => 'system', 'text' => $this->systemPrompt],
                    ['role' => 'user', 'text' => $gptRequest->message],
                ],
            ];

            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => "Bearer $iamToken",
                    'Content-Type' => 'application/json',
                ])
                ->post($this->gptUrl, $payload);

            $data = $response->json();

            if ($response->successful()) {
                return new OperationCreate(
                    messageId: $gptRequest->messageId,
                    yandexId: $data['id'] ?? null,
                    rawRequest: ['request' => $gptRequest->message],
                    rawResponse: $data,
                    gptResponse: null
                );
            }

            Log::error('Yandex GPT returned an error response.', [
                'status' => $response->status(),
                'body' => $data,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception while sending message to Yandex GPT.', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }
}
