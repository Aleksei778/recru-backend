<?php

declare(strict_types=1);

namespace Tests\Unit\Email;

use App\Base\Enum\Locale;
use App\Candidate\Models\Candidate;
use App\Email\Mail\InterviewMail;
use App\Email\Models\Email;
use App\Email\Services\{CreateService, SendService};
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

        $service = new CreateService();
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
        $mailable = $service->getInterviewMail(
            interview: $interview,
            user: $user,
            interviewUrl: 'http://test.com',
            locale: Locale::RU
        );

        $this->assertInstanceOf(InterviewMail::class, $mailable);
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
        $mailable = $service->getInterviewMail($interview, $user, 'http://link', Locale::EN);
        
        $service->sendInterviewMail($mailable);

        Mail::assertSent(InterviewMail::class, function ($mail) use ($candidate) {
            return $mail->hasTo($candidate->email);
        });
    }
}
