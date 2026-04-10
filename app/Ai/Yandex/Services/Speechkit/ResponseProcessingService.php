<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Services\Speechkit;

use App\Ai\Yandex\Services\ResponseProcessingInterface;

final readonly class ResponseProcessingService implements ResponseProcessingInterface
{
    public function processResponse(array $response): string
    {
        $responseText = '';

        $chunks = data_get($response, 'response.chunks');

        if ($chunks) {
            foreach ($chunks as $chunk) {
                $alternatives = data_get($chunk, 'alternatives');
                foreach ($alternatives as $alternative) {
                    $text = data_get($alternative, 'text');

                    $responseText .= $text . ' ';
                }
            }
        }

        return trim($responseText, " \n\r\t`");
    }
}
