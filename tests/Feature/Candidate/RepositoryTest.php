<?php

declare(strict_types=1);

namespace Tests\Feature\Candidate;

use App\Candidate\Models\Candidate;
use App\Candidate\Repositories\Repository;
use Tests\Feature\FeatureTestCase;


final class RepositoryTest extends FeatureTestCase
{
    private Repository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenant();
        $this->repository = new Repository();
    }

    public function test_find_returns_candidate_by_id(): void
    {
        $candidate = Candidate::factory()->forTenant($this->tenant, $this->user)->create();

        $result = $this->repository->find($candidate->id);

        $this->assertInstanceOf(Candidate::class, $result);
        $this->assertEquals($candidate->id, $result->id);
    }

    public function test_find_returns_null_for_nonexistent_id(): void
    {
        $result = $this->repository->find(99999);

        $this->assertNull($result);
    }

    public function test_search_finds_by_first_name(): void
    {
        Candidate::factory()->forTenant($this->tenant, $this->user)->create(['first_name' => 'Aleksandr', 'last_name' => 'Petrov']);
        Candidate::factory()->forTenant($this->tenant, $this->user)->create(['first_name' => 'Maria', 'last_name' => 'Ivanova']);

        $results = $this->repository->findWithQueryAndLimit('Aleks', 20);

        $this->assertCount(1, $results);
        $this->assertEquals('Aleksandr', $results->first()->first_name);
    }

    public function test_search_finds_by_last_name(): void
    {
        Candidate::factory()->forTenant($this->tenant, $this->user)->create(['first_name' => 'Ivan', 'last_name' => 'Sidorov']);
        Candidate::factory()->forTenant($this->tenant, $this->user)->create(['first_name' => 'Petr', 'last_name' => 'Kozlov']);

        $results = $this->repository->findWithQueryAndLimit('Sidorov', 20);

        $this->assertCount(1, $results);
        $this->assertEquals('Ivan', $results->first()->first_name);
    }

    public function test_search_finds_by_email(): void
    {
        Candidate::factory()->forTenant($this->tenant, $this->user)->create(['email' => 'test.dev@example.com']);
        Candidate::factory()->forTenant($this->tenant, $this->user)->create(['email' => 'other@example.com']);

        $results = $this->repository->findWithQueryAndLimit('test.dev', 20);

        $this->assertCount(1, $results);
        $this->assertEquals('test.dev@example.com', $results->first()->email);
    }

    public function test_search_is_case_insensitive(): void
    {
        Candidate::factory()->forTenant($this->tenant, $this->user)->create(['first_name' => 'Nikolay']);

        $results = $this->repository->findWithQueryAndLimit('nikolay', 20);

        $this->assertCount(1, $results);
    }

    public function test_search_respects_limit(): void
    {
        Candidate::factory()->forTenant($this->tenant, $this->user)->count(10)->create(['first_name' => 'Aleksey']);

        $results = $this->repository->findWithQueryAndLimit('Aleksey', 3);

        $this->assertCount(3, $results);
    }

    public function test_search_returns_empty_when_no_match(): void
    {
        Candidate::factory()->forTenant($this->tenant, $this->user)->create(['first_name' => 'Pavel']);

        $results = $this->repository->findWithQueryAndLimit('nonexistent_xyz', 20);

        $this->assertCount(0, $results);
    }
}
