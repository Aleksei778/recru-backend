<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Skill\Models\{Skill, SkillCategory};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'frontend',
            'backend',
            'fullstack',
            'mobile',
            'devops',
            'database',
            'cloud',
            'testing',
            'architecture',
            'security',
            'ai_ml',
            'data',
            'analytics',
            'design',
            'product',
            'management',
            'soft',
            'other',
        ];

        $categoryMap = [];

        foreach ($categories as $cat) {
            $category = SkillCategory::firstOrCreate(
                ['slug' => $cat],
                ['name' => ucfirst(str_replace('_', ' ', $cat))]
            );

            $categoryMap[$cat] = $category->id;
        }

        $skills = [

            // FRONTEND
            ['JavaScript', 'frontend', ['js']],
            ['TypeScript', 'frontend', ['ts']],
            ['React', 'frontend', ['react.js']],
            ['Next.js', 'frontend', ['next']],
            ['Vue', 'frontend', ['vue.js']],
            ['Angular', 'frontend', []],
            ['HTML', 'frontend', []],
            ['CSS', 'frontend', []],
            ['SASS', 'frontend', []],
            ['Tailwind', 'frontend', []],
            ['Redux', 'frontend', []],
            ['Zustand', 'frontend', []],

            // BACKEND
            ['Node.js', 'backend', ['node']],
            ['Python', 'backend', []],
            ['FastAPI', 'backend', []],
            ['Django', 'backend', []],
            ['Flask', 'backend', []],
            ['Java', 'backend', []],
            ['Spring', 'backend', ['spring boot']],
            ['Go', 'backend', ['golang']],
            ['PHP', 'backend', []],
            ['Laravel', 'backend', []],

            // DEVOPS / INFRA
            ['Docker', 'devops', []],
            ['Kubernetes', 'devops', ['k8s']],
            ['Terraform', 'devops', []],
            ['Ansible', 'devops', []],
            ['CI/CD', 'devops', []],
            ['GitHub Actions', 'devops', []],
            ['GitLab CI', 'devops', []],

            // CLOUD
            ['AWS', 'cloud', ['amazon web services']],
            ['GCP', 'cloud', ['google cloud']],
            ['Azure', 'cloud', []],

            // DATABASE
            ['PostgreSQL', 'database', ['postgres']],
            ['MySQL', 'database', []],
            ['MongoDB', 'database', []],
            ['Redis', 'database', []],
            ['SQLite', 'database', []],
            ['Elasticsearch', 'database', []],

            // MESSAGING
            ['Kafka', 'devops', []],
            ['RabbitMQ', 'devops', []],

            // TESTING
            ['Jest', 'testing', []],
            ['Cypress', 'testing', []],
            ['Playwright', 'testing', []],
            ['Mocha', 'testing', []],
            ['PHPUnit', 'testing', []],

            // MOBILE
            ['React Native', 'mobile', []],
            ['Flutter', 'mobile', []],
            ['Swift', 'mobile', []],
            ['Kotlin', 'mobile', []],

            // AI / ML
            ['TensorFlow', 'ai_ml', []],
            ['PyTorch', 'ai_ml', []],
            ['Scikit-learn', 'ai_ml', []],
            ['OpenAI API', 'ai_ml', []],

            // DATA
            ['Pandas', 'data', []],
            ['NumPy', 'data', []],
            ['Apache Spark', 'data', []],
            ['Hadoop', 'data', []],

            // ANALYTICS
            ['Power BI', 'analytics', []],
            ['Tableau', 'analytics', []],

            // DESIGN
            ['Figma', 'design', []],
            ['Adobe XD', 'design', []],

            // PRODUCT
            ['Product Management', 'product', []],
            ['User Research', 'product', []],

            // MANAGEMENT
            ['Scrum', 'management', []],
            ['Agile', 'management', []],
            ['Kanban', 'management', []],

            // SOFT
            ['Communication', 'soft', []],
            ['Leadership', 'soft', []],
            ['Problem Solving', 'soft', []],
        ];

        foreach ($skills as [$name, $category, $aliases]) {
            Skill::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'category_id' => $categoryMap[$category],
                    'aliases' => $aliases,
                ]
            );
        }
    }
}
