<?php

declare(strict_types=1);

namespace App\Email\Services;

use App\Base\Enum\Locale;
use App\Email\Mail\InterviewMail;
use App\Interview\Models\Interview;
use App\User\Models\User;
use Illuminate\Support\Facades\Mail;

final readonly class SendService
{
    public function getInterviewMail(
        Interview $interview,
        User $user,
        string $interviewUrl,
        Locale $locale,
    ): InterviewMail {
        return new InterviewMail(
            interview: $interview,
            interviewLink: $interviewUrl,
            user: $user,
        )->locale($locale->value);
    }

    public function sendInterviewMail(InterviewMail $mailable): void
    {
        Mail::to($mailable->user->email)
            ->send($mailable);
    }
}
