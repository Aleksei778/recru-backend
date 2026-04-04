<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Services;

use App\Ai\Yandex\Exceptions\FailedIamTokenRefreshException;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Client\Factory as HttpClient;

final readonly class IamTokenService
{
    private string $oauthToken;
    private string $iamTokenUrl;

    private const CACHE_KEY = 'yandex_token';

    public function __construct(
        private CacheContract $cache,
        private LoggerInterface $logger,
        private HttpClient $client
    ) {
        $this->oauthToken = config('services.yandex.oauth_token');
        $this->iamTokenUrl = config('services.yandex.iam_token_url');
    }

    /**
     * @return string
     * @throws FailedIamTokenRefreshException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getIamToken(): string
    {
        $cachedToken = $this->cache->get(self::CACHE_KEY);

        if ($cachedToken) {
            $this->logger->info('Yandex iam token retrieved from cache');

            return decrypt($cachedToken);
        }

        $this->logger->info('There is no yandex iam token in cache');

        return $this->refreshIamToken();
    }

    /**
     * @return string
     * @throws FailedIamTokenRefreshException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    private function refreshIamToken(): string
    {
        $response = $this->client
            ->timeout(seconds: 30)
            ->post($this->iamTokenUrl, [
                'yandexPassportOauthToken' => $this->oauthToken,
            ]);

        if (!$response->successful()) {
            $this->logger->error('Failed to refresh Yandex iam token', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('Yandex:IamTokenService: Failed to refresh iam token');
        }

        $data = $response->json();

        if (array_key_exists('iamToken', $data)) {
            $iamToken = $data['iamToken'];

            $this->cache->put(
                self::CACHE_KEY,
                encrypt($iamToken),
                now()->addHour(11)
            );

            $this->logger->info('Yandex iam token was correctly refreshed and cached');

            return $iamToken;
        }

        throw new FailedIamTokenRefreshException('Yandex:IamTokenService: There is no iam token in response body');
    }
}
