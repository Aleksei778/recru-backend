<?php

declare(strict_types=1);

namespace App\Email\Mail;

use App\Candidate\Models\Candidate;
use App\Interview\Models\Interview;
use App\User\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class QuestionsReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Interview $interview,
        public readonly User $user,
    ) {
    }

    public function build(): self
    {
        return $this->subject(__('emails.questions_ready.subject', [
                'firstName' => $this->interview->candidate->first_name,
                'lastName'  => $this->interview->candidate->last_name,
            ]))
            ->view(
                view: 'emails.questions_ready',
                data: [
                    'user' => $this->user,
                    'interview' => $this->interview,
                ]
            );
    }
}
