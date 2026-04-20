<?php

declare(strict_types=1);

namespace Tests\Unit\Email;

use App\Common\Enum\Locale;
use App\Candidate\Models\Candidate;
use App\Email\Mail\InterviewInvitationMail;
use App\Email\Models\Email;
use App\Email\Services\{CrudService, SendService};
use App\Interview\Models\Interview;
use App\User\Models\User;
use App\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_service_saves_email_to_database(): void
    {
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create(['email' => 'candidate@example.com']);
        $vacancy = Vacancy::factory()->create(['title' => 'PHP Developer']);
        $interview = Interview::factory()->create([
            'candidate_id' => $candidate->id,
            'vacancy_id' => $vacancy->id,
        ]);

        $service = new CrudService();
        $email = $service->create(
            user: $user,
            interview: $interview,
            locale: Locale::RU,
        );

        $this->assertInstanceOf(Email::class, $email);
        $this->assertDatabaseHas('emails', [
            'id' => $email->id,
            'user_id' => $user->id,
            'candidate_id' => $candidate->id,
            'vacancy_id' => $vacancy->id,
            'interview_id' => $interview->id,
            'locale' => 'ru',
            'title' => 'Test Title',
        ]);
    }

    public function test_send_service_generates_correct_mailable(): void
    {
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->create(['title' => 'Developer']);
        $interview = Interview::factory()->create([
            'candidate_id' => $candidate->id,
            'vacancy_id' => $vacancy->id,
        ]);

        $service = new SendService();
        $mailable = $service->getInterviewInvitationMail(
            interview: $interview,
            user: $user,
            interviewUrl: 'http://test.com',
            locale: Locale::RU
        );

        $this->assertInstanceOf(InterviewInvitationMail::class, $mailable);
        $this->assertEquals('ru', $mailable->locale);
    }

    public function test_send_service_sends_email(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $candidate = Candidate::factory()->create(['email' => 'candidate@example.com']);
        $vacancy = Vacancy::factory()->create();
        $interview = Interview::factory()->create([
            'candidate_id' => $candidate->id,
            'vacancy_id' => $vacancy->id,
        ]);

        $service = new SendService();
        $mailable = $service->getInterviewInvitationMail($interview, $user, 'http://link', Locale::EN);
        
        $service->sendInterviewInvitationMail($mailable);

        Mail::assertSent(InterviewInvitationMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_interview_invitation_mail_has_correct_content(): void
    {
        $user = User::factory()->create();
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->create(['title' => 'PHP Developer']);
        $interview = Interview::factory()->create([
            'candidate_id' => $candidate->id,
            'vacancy_id' => $vacancy->id,
        ]);

        $interviewUrl = 'http://test-interview.com/token';
        
        $mailable = new InterviewInvitationMail(
            interview: $interview,
            interviewLink: $interviewUrl,
            user: $user
        );

        $mailable->assertSeeInHtml($candidate->first_name);
        $mailable->assertSeeInHtml($vacancy->title);
        $mailable->assertSeeInHtml($interviewUrl);
    }
}
