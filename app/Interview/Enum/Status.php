<?php

declare(strict_types=1);

namespace App\Interview\Enum;

enum Status: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'inprogress';
    case COMPLETED = 'completed';
}
