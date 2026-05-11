<?php

declare(strict_types=1);

namespace App\Email\Services;

use App\Common\Enum\Locale;
use App\Email\Mail\CandidateApprovedMail;
use App\Email\Mail\CandidateRejectedMail;
use App\Email\Mail\InterviewFinishedMail;
use App\Email\Mail\InterviewInvitationMail;
use App\Email\Mail\QuestionsReadyMail;
use App\Interview\Models\Interview;
use Illuminate\Mail\Mailable;
use App\User\Models\User;
use Illuminate\Support\Facades\Mail;

final readonly class SendService
{
    public function getInterviewInvitationMail(
        Interview $interview,
        User $user,
        string $interviewUrl,
    ): InterviewInvitationMail {
        return new InterviewInvitationMail(
            interview: $interview,
            interviewLink: $interviewUrl,
            user: $user,
        )->locale($interview->candidate->value);
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

    public function getCandidateApprovedMail(Interview $interview, Locale $locale): CandidateApprovedMail
    {
        return new CandidateApprovedMail($interview)->locale($locale->value);
    }

    public function getCandidateRejectedMail(Interview $interview, Locale $locale): CandidateRejectedMail
    {
        return new CandidateRejectedMail($interview)->locale($locale->value);
    }

    public function sendCandidateDecisionMail(CandidateApprovedMail|CandidateRejectedMail $mailable): void
    {
        Mail::to($mailable->interview->candidate->email)
            ->send($mailable);
    }

    public function getInterviewFinishedMail(
        Interview $interview,
        User $user,
        Locale $locale,
    ): InterviewFinishedMail {
        return new InterviewFinishedMail(
            interview: $interview,
            hr: $user,
        )->locale($locale->value);
    }

    public function sendInterviewFinishedMail(InterviewFinishedMail $mailable): void
    {
        Mail::to($mailable->hr->email)
            ->send($mailable);
    }
}
