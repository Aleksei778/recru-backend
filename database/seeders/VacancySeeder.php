<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Skill\Models\Skill;
use App\Tenant\Models\Tenant;
use App\User\Models\User;
use App\Vacancy\Models\Vacancy;
use Illuminate\Database\Seeder;

final class VacancySeeder extends Seeder
{
    private static array $vacancySkills = [
        'Backend Developer (PHP/Laravel)' => [
            'PHP', 'Laravel', 'PostgreSQL', 'Redis', 'Docker', 'PHPUnit', 'RabbitMQ',
        ],
        'Frontend Developer (React/TypeScript)' => [
            'TypeScript', 'React', 'Redux', 'HTML', 'CSS', 'Tailwind', 'Jest',
        ],
        'Full Stack Developer (Node.js + React)' => [
            'Node.js', 'React', 'TypeScript', 'PostgreSQL', 'MongoDB', 'Docker',
        ],
        'DevOps Engineer' => [
            'Kubernetes', 'Docker', 'Terraform', 'Ansible', 'CI/CD', 'GitHub Actions', 'AWS',
        ],
        'Data Engineer' => [
            'Python', 'Apache Spark', 'Kafka', 'PostgreSQL', 'Pandas', 'NumPy', 'AWS',
        ],
        'QA Engineer (Automation)' => [
            'Playwright', 'Cypress', 'Jest', 'TypeScript', 'CI/CD',
        ],
        'Mobile Developer (Flutter)' => [
            'Flutter', 'React Native',
        ],
        'ML Engineer' => [
            'Python', 'TensorFlow', 'PyTorch', 'Scikit-learn', 'Pandas', 'NumPy',
        ],
        'iOS Developer (Swift)' => [
            'Swift',
        ],
        'Android Developer (Kotlin)' => [
            'Kotlin',
        ],
        'Backend Developer (Python/FastAPI)' => [
            'Python', 'FastAPI', 'PostgreSQL', 'Redis', 'Docker', 'Kafka', 'Kubernetes',
        ],
        'Team Lead (Backend)' => [
            'Scrum', 'Agile', 'Leadership', 'Communication', 'PostgreSQL', 'Docker',
        ],
    ];

    public function run(): void
    {
        $skillsByName = Skill::all()->keyBy('name');

        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $admin = User::where('tenant_id', $tenant->id)->first();

            $published = Vacancy::factory()
                ->published()
                ->forTenant($tenant, $admin)
                ->count(3)
                ->create();

            $draft = Vacancy::factory()
                ->forTenant($tenant, $admin)
                ->count(2)
                ->create();

            foreach ($published->merge($draft) as $vacancy) {
                $names = self::$vacancySkills[$vacancy->title] ?? [];
                $ids = $skillsByName->only($names)->pluck('id');

                if ($ids->isNotEmpty()) {
                    $vacancy->skills()->sync($ids);
                }
            }
        }
    }
}
