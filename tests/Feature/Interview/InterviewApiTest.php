<?php

declare(strict_types=1);

namespace Tests\Feature\Interview;

use App\Candidate\Models\Candidate;
use App\Interview\Enum\Status;
use App\Interview\Models\Interview;
use App\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\WithTenant;
use Tests\TestCase;

class InterviewApiTest extends TestCase
{
    use RefreshDatabase, WithTenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenant();
    }

    // --- index ---

    public function test_index_returns_paginated_interviews(): void
    {
        $candidate = Candidate::factory()->forTenant($this->tenant, $this->user)->create();
        $vacancy = Vacancy::factory()->forTenant($this->tenant, $this->user)->create();
        Interview::factory()->count(3)->create([
            'candidate_id' => $candidate->id,
            'vacancy_id' => $vacancy->id,
        ]);

        $response = $this->tenantJson('GET', 'api/hr/interviews');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'status']],
                'meta',
            ]);
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->json('GET', $this->tenantUrl('api/hr/interviews'));

        $response->assertUnauthorized();
    }

    // --- show ---

    public function test_show_returns_interview_with_questions(): void
    {
        $interview = Interview::factory()->create([
            'candidate_id' => Candidate::factory()->forTenant($this->tenant, $this->user)->create()->id,
            'vacancy_id' => Vacancy::factory()->forTenant($this->tenant, $this->user)->create()->id,
        ]);

        $response = $this->tenantJson('GET', "api/hr/interviews/{$interview->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $interview->id)
            ->assertJsonStructure([
                'data' => ['id', 'status', 'candidate', 'vacancy', 'questions'],
            ]);
    }

    public function test_show_returns_404_for_nonexistent_interview(): void
    {
        $response = $this->tenantJson('GET', 'api/hr/interviews/99999');

        $response->assertNotFound();
    }

    // --- public routes (no auth needed) ---

    public function test_next_question_returns_404_for_invalid_token(): void
    {
        $response = $this->json(
            'GET',
            $this->tenantUrl('api/candidate/interviews/invalid-token-xyz/questions/next')
        );

        $response->assertNotFound();
    }

    public function test_next_question_returns_422_when_interview_not_in_valid_state(): void
    {
        $interview = Interview::factory()->create(['status' => Status::Pending]);

        $response = $this->json(
            'GET',
            $this->tenantUrl("api/candidate/interviews/{$interview->token}/questions/next")
        );

        $response->assertUnprocessable();
    }

    // --- close ---

    public function test_close_returns_422_for_invalid_decision(): void
    {
        $interview = Interview::factory()->evaluated()->create();

        $response = $this->tenantJson('POST', "api/hr/interviews/{$interview->id}/close", [
            'decision' => 'invalid_decision',
        ]);

        $response->assertUnprocessable();
    }

    // --- answer (public) ---

    public function test_answer_returns_422_when_interview_not_in_progress(): void
    {
        $interview = Interview::factory()->create(['status' => Status::Pending]);
        $question = \App\Interview\Models\Question::factory()->create(['interview_id' => $interview->id]);

        $response = $this->json(
            'POST',
            $this->tenantUrl("api/candidate/interviews/{$interview->token}/questions/{$question->id}/answer")
        );

        $response->assertUnprocessable();
    }
}