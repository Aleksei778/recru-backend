<?php

declare(strict_types=1);

namespace App\Ai\Gpt\Contracts;

use App\Ai\Gpt\Dto\Message;
use App\Ai\Operation\Models\Operation;

interface AsyncInterface
{
    /** @param Message[] $messages */
    public function completionAsync(array $messages): ?string;
    public function getCompletionResult(Operation $operation): ?string;
}
