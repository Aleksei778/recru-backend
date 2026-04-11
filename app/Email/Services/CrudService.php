<?php

declare(strict_types=1);

namespace App\Email\Services;

use App\Base\Enum\Locale;
use App\Email\Models\Email;
use App\Interview\Models\Interview;
use App\User\Models\User;

final readonly class CrudService
{
    public function create(
        User $user,
        Interview $interview,
        Locale $locale
    ): Email {
        $email = new Email([
            'user_id' => $user->id,
            'interview_id' => $interview->id,
            'locale' => $locale,
        ]);

        $email->save();

        return $email;
    }
}
