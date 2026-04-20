<?php

declare(strict_types=1);

namespace App\Email\Mail;

use App\Interview\Models\Interview;
use App\User\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class InterviewInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Interview $interview,
        public readonly string $interviewLink,
        public readonly User $user,
    ) {
    }

    public function build(): self
    {
        return $this->subject(__('emails.interview.subject', ['vacancy' => $this->interview->vacancy->title]))
            ->view(
                view: 'emails.interview_invitation',
                data: [
                    'candidate' => $this->interview->candidate,
                    'user' => $this->user,
                    'interview' => $this->interview,
                    'interviewUrl' => $this->interviewLink,
                    'vacancy' => $this->interview->vacancy,
                ]
            );
    }
}
