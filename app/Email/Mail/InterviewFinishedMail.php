<?php

declare(strict_types=1);

namespace App\Email\Mail;

use App\Interview\Models\Interview;
use App\User\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class InterviewFinishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Interview $interview,
        public readonly User $hr,
    ) {
    }

    public function build(): self
    {
        return $this->subject(__('emails.interview_finished.subject', [
                'firstName' => $this->interview->candidate->first_name,
                'lastName'  => $this->interview->candidate->last_name,
                'vacancy'   => $this->interview->vacancy->title,
            ]))
            ->view(
                view: 'emails.interview_finish',
                data: [
                    'hr'        => $this->hr,
                    'candidate' => $this->interview->candidate,
                    'vacancy'   => $this->interview->vacancy,
                    'interview' => $this->interview,
                ]
            );
    }
}
