<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Candidate\Models\Candidate;
use App\Interview\Models\{Answer, Interview, Question};
use App\Tenant\Models\Tenant;
use App\Vacancy\Models\Vacancy;
use Illuminate\Database\Seeder;

final class InterviewSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $candidates = Candidate::where('tenant_id', $tenant->id)->get();
            $vacancies = Vacancy::where('tenant_id', $tenant->id)->get();

            $candidates->each(function (Candidate $candidate) use ($vacancies) {
                $vacancy = $vacancies->random();

                $interview = Interview::factory()->create([
                    'candidate_id' => $candidate->id,
                    'vacancy_id' => $vacancy->id,
                ]);

                Question::factory()
                    ->count(fake()->numberBetween(3, 6))
                    ->create(['interview_id' => $interview->id])
                    ->each(function (Question $question) {
                        Answer::factory()->create(['question_id' => $question->id]);
                    });
            });
        }
    }
}