<?php

declare(strict_types=1);

namespace App\User\Enum;

enum UserRole: string
{
    case ADMIN = 'admin';
    case RECRUITER = 'recruiter';
}
