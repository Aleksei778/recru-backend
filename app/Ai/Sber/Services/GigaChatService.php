<?php

declare(strict_types=1);

namespace App\Ai\Sber\Services;

use App\Ai\Sber\Exceptions\{AuthException};
use App\Ai\Sber\Exceptions\ChatException;
use App\Ai\Sber\Exceptions\EmbeddingsException;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;

final readonly class GigaChatService
{
    private string $baseUrl;
    private string $authUrl;
    private string $clientId;
    private string $secret;

    public function __construct(
        private CacheContract $cache,
        private HttpClient $http,
        private LoggerInterface $logger,
    ) {
        $this->baseUrl = config('services.gigachat.base_url');
        $this->authUrl = config('services.gigachat.auth_url');
        $this->clientId = config('services.gigachat.client_id');
        $this->secret = config('services.gigachat.secret');
    }

    private function token(): string
    {
        return $this->cache->remember('gigachat_token', 1680, function () {
            $response = $this->http->withOptions(['verify' => false])
                ->asForm()
                ->withHeaders([
                    'RqUID' => Str::uuid()->toString(),
                    'Authorization' => 'Basic ' . base64_encode("{$this->clientId}:{$this->secret}"),
                ])
                ->post($this->authUrl, ['scope' => 'GIGACHAT_API_PERS']);

            if ($response->failed()) {
                $this->logger->error('GigaChat auth failed', ['response' => $response->body()]);

                throw new AuthException('Ai:GigaChatService: failed auth');
            }

            return $response
                ->json('access_token');
        });
    }

    public function chat(array $messages, float $temperature = 0.7, int $maxTokens = 1024): string
    {
        $response = $this->http
            ->withOptions(['verify' => false])
            ->withToken($this->token())
            ->timeout(30)
            ->post($this->baseUrl . '/chat/completions', [
                'model' => 'GigaChat',
                'messages' => $messages,
                'temperature' => $temperature,
                'max_tokens' => $maxTokens,
            ]);

        if ($response->failed()) {
            $this->logger->error('GigaChat chat failed', ['response' => $response->body()]);

            throw new ChatException('Ai:GigaChatService: failed chat');
        }

        return $response->json('choices.0.message.content', '');
    }

    public function embed(string $text): array
    {
        $response = $this->http
            ->withOptions(['verify' => false])
            ->timeout(15)
            ->post($this->baseUrl . '/embeddings', [
                'model' => 'Embeddings',
                'input' => [$text],
            ]);

        if ($response->failed()) {
            $this->logger->error('GigaChat embeddings failed', ['response' => $response->body()]);

            throw new EmbeddingsException('Ai:GigaChatService: embeddings');
        }

        return $response->json('data.0.embedding', []);
    }
}
