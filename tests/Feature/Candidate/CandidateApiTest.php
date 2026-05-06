<?php

declare(strict_types=1);

namespace Tests\Feature\Candidate;

use App\Candidate\Enum\EducationLevel;
use App\Candidate\Enum\Source;
use App\Candidate\Models\Candidate;
use App\Tenant\Models\Tenant;
use App\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\WithTenant;
use Tests\TestCase;

class CandidateApiTest extends TestCase
{
    use RefreshDatabase, WithTenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenant();
    }

    // --- index ---

    public function test_index_returns_paginated_list_of_candidates(): void
    {
        Candidate::factory()->count(3)->forTenant($this->tenant, $this->user)->create();

        $response = $this->tenantJson('GET', 'api/candidates');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'first_name', 'last_name', 'email']],
                'meta' => ['current_page', 'total'],
            ]);
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->json('GET', $this->tenantUrl('api/candidates'));

        $response->assertUnauthorized();
    }

    // --- store ---

    public function test_store_creates_candidate_and_returns_resource(): void
    {
        $payload = $this->validCandidatePayload();

        $response = $this->tenantJson('POST', 'api/candidates', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.first_name', 'Andrei')
            ->assertJsonPath('data.last_name', 'Smirnov');

        $this->assertDatabaseHas('candidates', [
            'email' => 'andrei@example.com',
            'first_name' => 'Andrei',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->tenantJson('POST', 'api/candidates', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'source', 'education_level']);
    }

    public function test_store_rejects_duplicate_email(): void
    {
        Candidate::factory()->create(['email' => 'taken@example.com']);

        $payload = $this->validCandidatePayload(['email' => 'taken@example.com']);

        $response = $this->tenantJson('POST', 'api/candidates', $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    // --- show ---

    public function test_show_returns_candidate_with_relationships(): void
    {
        $candidate = Candidate::factory()->forTenant($this->tenant, $this->user)->create();

        $response = $this->tenantJson('GET', "api/candidates/{$candidate->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $candidate->id)
            ->assertJsonStructure([
                'data' => ['id', 'first_name', 'last_name', 'email', 'interviews', 'work_places', 'socials'],
            ]);
    }

    public function test_show_returns_404_for_nonexistent_candidate(): void
    {
        $response = $this->tenantJson('GET', 'api/candidates/99999');

        $response->assertNotFound();
    }

    // --- update ---

    public function test_update_modifies_candidate_fields(): void
    {
        $candidate = Candidate::factory()->forTenant($this->tenant, $this->user)->create();

        $response = $this->tenantJson('PUT', "api/candidates/{$candidate->id}", [
            'first_name' => 'Updated',
            'last_name' => 'Name',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.first_name', 'Updated');

        $this->assertDatabaseHas('candidates', [
            'id' => $candidate->id,
            'first_name' => 'Updated',
        ]);
    }

    // --- destroy ---

    public function test_destroy_deletes_candidate_and_returns_no_content(): void
    {
        $candidate = Candidate::factory()->forTenant($this->tenant, $this->user)->create();

        $response = $this->tenantJson('DELETE', "api/candidates/{$candidate->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('candidates', ['id' => $candidate->id]);
    }

    // --- search ---

    public function test_search_returns_matching_candidates(): void
    {
        Candidate::factory()->create(['first_name' => 'Ekaterina', 'last_name' => 'Volkova']);
        Candidate::factory()->create(['first_name' => 'Dmitry', 'last_name' => 'Sokolov']);

        $response = $this->tenantJson('GET', 'api/candidates/search', ['q' => 'Ekaterina']);

        $response->assertOk();

        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertEquals('Ekaterina', $data[0]['first_name']);
    }

    public function test_search_requires_q_parameter(): void
    {
        $response = $this->tenantJson('GET', 'api/candidates/search', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['q']);
    }

    public function test_search_returns_empty_when_no_match(): void
    {
        Candidate::factory()->create(['first_name' => 'Olga']);

        $response = $this->tenantJson('GET', 'api/candidates/search', ['q' => 'xyz_nonexistent']);

        $response->assertOk();
        $this->assertEmpty($response->json());
    }

    private function validCandidatePayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Andrei',
            'last_name' => 'Smirnov',
            'email' => 'andrei@example.com',
            'source' => Source::HH->value,
            'experience_years' => 3,
            'education_level' => EducationLevel::BACHELOR->value,
            'workplaces' => [
                [
                    'position' => 'PHP Developer',
                    'company_name' => 'Tech Corp',
                    'started_at' => '2020-01-01',
                ],
            ],
            'socials' => [
                [
                    'name' => 'LinkedIn',
                    'url' => 'https://linkedin.com/in/andrei',
                ],
            ],
        ], $overrides);
    }
}