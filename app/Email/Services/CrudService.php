<?php

declare(strict_types=1);

namespace App\Email\Services;

use App\Candidate\Models\Candidate;
use App\Email\Enum\Type;
use App\Email\Models\Email;
use App\Interview\Models\Interview;
use App\User\Models\User;

final readonly class CrudService
{
    public function createInvitation(User $user, Interview $interview): Email
    {
        $email = new Email([
            'sender_id' => $user->id,
            'interview_id' => $interview->id,
            'type' => Type::InterviewInvite,
            'subject' => __('emails.interview_invitation.subject', ['vacancy' => $interview->vacancy->title]),
            'recipient_id' => $interview->candidate->id,
            'recipient_type' => Candidate::class,
            'locale' => $interview->candidate->locale,
        ]);

        $email->save();

        return $email;
    }
}
