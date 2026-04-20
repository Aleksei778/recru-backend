<?php

declare(strict_types=1);

namespace App\Email\Services;

use App\Common\Enum\Locale;
use App\Email\Mail\InterviewInvitationMail;
use App\Email\Mail\QuestionsReadyMail;
use App\Interview\Models\Interview;
use App\User\Models\User;
use Illuminate\Support\Facades\Mail;

final readonly class SendService
{
    public function getInterviewInvitationMail(
        Interview $interview,
        User $user,
        string $interviewUrl,
        Locale $locale,
    ): InterviewInvitationMail {
        return new InterviewInvitationMail(
            interview: $interview,
            interviewLink: $interviewUrl,
            user: $user,
        )->locale($locale->value);
    }

    public function sendInterviewInvitationMail(InterviewInvitationMail $mailable): void
    {
        Mail::to($mailable->interview->candidate->email)
            ->send($mailable);
    }

    public function getQuestionsReadyMail(
        Interview $interview,
        User $user,
        Locale $locale,
    ): QuestionsReadyMail {
        return new QuestionsReadyMail(
            interview: $interview,
            user: $user,
        )->locale($locale->value);
    }

    public function sendQuestionsReadyMail(QuestionsReadyMail $mailable): void
    {
        Mail::to($mailable->user->email)
            ->send($mailable);
    }
}
