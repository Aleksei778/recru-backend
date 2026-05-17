<?php

declare(strict_types=1);

namespace App\Email\Services;

use App\Common\Enum\Locale;
use App\Email\Mail\{
    CandidateApprovedMail,
    CandidateRejectedMail,
    InterviewFinishedMail,
    InterviewInvitationMail
};
use App\Interview\Models\Interview;
use Illuminate\Support\Facades\Mail;

final readonly class SendService
{
    public function getInterviewInvitationMail(
        Interview $interview,
        string $interviewUrl,
    ): InterviewInvitationMail {
        return new InterviewInvitationMail(
            interview: $interview,
            interviewLink: $interviewUrl,
        )->locale($interview->candidate->locale?->value ?? Locale::RU->value);
    }

    public function sendInterviewInvitationMail(InterviewInvitationMail $mailable): void
    {
        Mail::to($mailable->interview->candidate->email)
            ->send($mailable);
    }

    public function getCandidateApprovedMail(Interview $interview): CandidateApprovedMail
    {
        return new CandidateApprovedMail($interview)
            ->locale($interview->candidate->locale?->value ?? Locale::RU->value);
    }

    public function getCandidateRejectedMail(Interview $interview): CandidateRejectedMail
    {
        return new CandidateRejectedMail($interview)
            ->locale($interview->candidate->locale?->value ?? Locale::RU->value);
    }

    public function sendCandidateDecisionMail(CandidateApprovedMail|CandidateRejectedMail $mailable): void
    {
        Mail::to($mailable->interview->candidate->email)
            ->send($mailable);
    }

    public function getInterviewFinishedMail(Interview $interview): InterviewFinishedMail
    {
        return new InterviewFinishedMail($interview)
            ->locale($interview->vacancy->createdBy->locale?->value ?? Locale::RU->value);
    }

    public function sendInterviewFinishedMail(InterviewFinishedMail $mailable): void
    {
        Mail::to($mailable->interview->vacancy->createdBy->email)
            ->send($mailable);
    }
}
