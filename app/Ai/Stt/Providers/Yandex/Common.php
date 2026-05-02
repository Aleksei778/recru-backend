<?php

declare(strict_types=1);

namespace App\Ai\Stt\Providers\Yandex;

use Illuminate\Http\Client\Factory as HttpClient;
use Psr\Log\LoggerInterface;
use App\Common\Services\Storage;

readonly class Common
{
    protected string $folderId;
    protected string $apiKey;

    public function __construct(
        protected HttpClient $client,
        protected LoggerInterface $logger,
        protected Storage $storage,
    ) {
        $this->folderId = config('services.yandex.folder_id');
        $this->apiKey = config('services.yandex.api_key');
    }
}
