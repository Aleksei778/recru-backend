<?php

declare(strict_types=1);

namespace Tests\Feature\Candidate;

use App\Candidate\Dto\Candidate\{Create, Update};
use App\Candidate\Enum\{EducationLevel, Source, Status};
use App\Candidate\Models\Candidate;
use App\Candidate\Services\CrudService;
use Tests\Feature\FeatureTestCase;

final class CrudServiceTest extends FeatureTestCase
{
    private CrudService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CrudService();
    }

    public function test_create_saves_candidate_to_database(): void
    {
        $dto = new Create(
            firstName: 'Ivan',
            lastName: 'Petrov',
            middleName: null,
            email: 'ivan@example.com',
            phone: '+79001234567',
            source: Source::HH,
            experienceYears: 3.0,
            educationLevel: EducationLevel::BACHELOR,
        );

        $candidate = $this->service->create($dto);

        $this->assertInstanceOf(Candidate::class, $candidate);
        $this->assertDatabaseHas('candidates', [
            'first_name' => 'Ivan',
            'last_name' => 'Petrov',
            'email' => 'ivan@example.com',
        ]);
    }

    public function test_create_sets_status_to_new_by_default(): void
    {
        $dto = new Create(
            firstName: 'Maria',
            lastName: 'Ivanova',
            middleName: null,
            email: 'maria@example.com',
            phone: null,
            source: Source::HABR,
            experienceYears: 1.5,
            educationLevel: EducationLevel::MASTER,
        );

        $candidate = $this->service->create($dto);

        $this->assertEquals(Status::NEW, $candidate->status);
        $this->assertDatabaseHas('candidates', [
            'id' => $candidate->id,
            'status' => Status::NEW->value,
        ]);
    }

    public function test_update_modifies_candidate_fields(): void
    {
        $candidate = Candidate::factory()->create([
            'first_name' => 'Old',
            'last_name' => 'Name',
        ]);

        $dto = Update::fromArray([
            'first_name' => 'New',
            'last_name' => 'Surname',
            'experience_years' => 5,
        ]);

        $updated = $this->service->update($candidate, $dto);

        $this->assertEquals('New', $updated->first_name);
        $this->assertEquals('Surname', $updated->last_name);
        $this->assertDatabaseHas('candidates', [
            'id' => $candidate->id,
            'first_name' => 'New',
            'last_name' => 'Surname',
        ]);
    }

    public function test_update_only_changes_provided_fields(): void
    {
        $candidate = Candidate::factory()->create([
            'first_name' => 'Ivan',
            'email' => 'original@example.com',
        ]);

        $dto = Update::fromArray(['first_name' => 'Petr']);

        $this->service->update($candidate, $dto);

        $this->assertDatabaseHas('candidates', [
            'id' => $candidate->id,
            'first_name' => 'Petr',
            'email' => 'original@example.com',
        ]);
    }

    public function test_delete_removes_candidate_from_database(): void
    {
        $candidate = Candidate::factory()->create();
        $id = $candidate->id;

        $result = $this->service->delete($candidate);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('candidates', ['id' => $id]);
    }
}
