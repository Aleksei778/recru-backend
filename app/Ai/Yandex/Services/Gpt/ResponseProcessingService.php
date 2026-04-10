<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Services\Gpt;

use App\Ai\Yandex\Services\ResponseProcessingInterface;

final readonly class ResponseProcessingService implements ResponseProcessingInterface
{
    public function processResponse(array $response): string
    {
        $responseText = '';

        $alternatives = data_get($response, 'response.alternatives');

        foreach ($alternatives as $alternative) {
            $text = data_get($alternative, 'message.text');

            $responseText .= $text . ' ';
        }

        return trim($responseText, " \n\r\t`");
    }
}
