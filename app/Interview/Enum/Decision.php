<?php

declare(strict_types=1);

namespace App\Interview\Enum;

enum Decision: string
{
    case Reject = 'reject';
    case Approve = 'approve';
}
