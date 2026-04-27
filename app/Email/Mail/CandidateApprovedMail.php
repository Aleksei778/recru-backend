<?php

declare(strict_types=1);

namespace App\Email\Mail;

use App\Interview\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class CandidateApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Interview $interview,
    ) {
    }

    public function build(): self
    {
        return $this->subject(__('emails.candidate_approved.subject', ['vacancy' => $this->interview->vacancy->title]))
            ->view(
                view: 'emails.candidate_approved',
                data: [
                    'candidate' => $this->interview->candidate,
                    'vacancy'   => $this->interview->vacancy,
                    'interview' => $this->interview,
                ]
            );
    }
}
