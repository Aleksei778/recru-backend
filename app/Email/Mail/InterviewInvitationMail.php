<?php

declare(strict_types=1);

namespace App\Email\Mail;

use App\Interview\Models\Interview;
use Illuminate\{
    Bus\Queueable,
    Mail\Mailable,
    Queue\SerializesModels
};

final class InterviewInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Interview $interview,
        public readonly string $interviewLink,
    ) {
    }

    public function build(): self
    {
        return $this->subject(__('emails.interview_invitation.subject', ['vacancy' => $this->interview->vacancy->title]))
            ->view(
                view: 'emails.interview_invitation',
                data: [
                    'candidate' => $this->interview->candidate,
                    'user' => $this->interview->vacancy->createdBy,
                    'interview' => $this->interview,
                    'interviewUrl' => $this->interviewLink,
                    'vacancy' => $this->interview->vacancy,
                ]
            );
    }
}
