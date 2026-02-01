<?php

declare(strict_types=1);

namespace App\Yandex\Services;

use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Support\Facades\Http;
use Psr\Log\LoggerInterface;

final readonly class IamTokenService
{
    private string $oauthToken;
    private string $iamTokenUrl;

    public function __construct(
        private CacheContract $cache,
        private LoggerInterface $logger,
    ) {
        $this->oauthToken = config('services.yandex.oauth_token');
        $this->iamTokenUrl = 'https://iam.api.cloud.yandex.net/iam/v1/tokens';
    }

    public function getIamToken(): string
    {
        $cachedToken = $this->cache->get('yandex_iam_token');

        if ($cachedToken) {
            $this->logger->info('Yandex iam token retrieved from cache');

            return decrypt($cachedToken);
        }

        $this->logger->info('There is no yandex iam token in cache');

        return $this->refreshIamToken();
    }

    private function refreshIamToken(): string
    {
        $response = Http::timeout(30)
            ->post($this->iamTokenUrl, [
                'yandexPassportOauthToken' => $this->oauthToken,
            ]);

        if (!$response->successful()) {
            $this->logger->error('Failed to refresh yandex iam token', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('IamTokenService:refreshIamToken: Failed to refresh yandex iam token');
        }

        $data = $response->json();
        $iamToken = $data['iamToken'];

        $this->cache->put(
            'yandex_iam_token',
            encrypt($iamToken),
            now()->addHours(11),
        );

        $this->logger->info('Yandex iam token was correctly refreshed and cached');

        return $iamToken;
    }
}
