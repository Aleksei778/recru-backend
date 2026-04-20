<?php

declare(strict_types=1);

namespace App\Ai\Gpt\Contracts;

use App\Ai\Gpt\Dto\Message;

interface SyncInterface
{
    /** @param Message[] $messages */
    public function completionSync(array $messages): ?string;
}
