<?php

declare(strict_types=1);

namespace App\Ai\Operation\Enum;

enum CheckResult: string
{
    case Done = 'done';
    case NotReady = 'notready';
    case Failed = 'failed';
    case AlreadyDone = 'alreadydone';
}
