<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Tenant\Models\Tenant;
use App\User\Models\User;
use App\Vacancy\Enum\{EmploymentType, Status, WorkMode};
use App\Vacancy\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

final class VacancyFactory extends Factory
{
    protected $model = Vacancy::class;

    private static array $positions = [
        [
            'title' => 'Backend Developer (PHP/Laravel)',
            'description' => "Ищем опытного Backend-разработчика для работы над высоконагруженными API-сервисами.\n\nОбязанности:\n— Разработка и поддержка REST API на Laravel\n— Проектирование схем БД и оптимизация запросов\n— Интеграция с внешними сервисами и очередями (RabbitMQ, Kafka)\n— Участие в code review, написание тестов (PHPUnit)\n\nТребования:\n— Опыт разработки на PHP от 3 лет\n— Уверенное знание Laravel, Eloquent ORM\n— Опыт работы с PostgreSQL, Redis\n— Знакомство с Docker и CI/CD\n— Понимание SOLID, DDD, чистой архитектуры",
        ],
        [
            'title' => 'Frontend Developer (React/TypeScript)',
            'description' => "Ищем Frontend-разработчика в продуктовую команду для разработки SPA.\n\nОбязанности:\n— Разработка UI-компонентов на React + TypeScript\n— Работа с REST API\n— Написание тестов (Jest, Playwright)\n— Участие в техническом планировании\n\nТребования:\n— Опыт коммерческой разработки на React от 2 лет\n— Уверенное знание TypeScript\n— Опыт работы с Redux или Zustand\n— Знание HTML, CSS, Tailwind",
        ],
        [
            'title' => 'Full Stack Developer (Node.js + React)',
            'description' => "Приглашаем Full Stack разработчика для работы над SaaS-платформой.\n\nОбязанности:\n— Разработка фич от бэка до фронта\n— Проектирование и поддержка API (Node.js)\n— Разработка React-компонентов\n— Участие в планировании спринтов\n\nТребования:\n— Опыт работы с Node.js и React от 2 лет\n— Знание TypeScript\n— Опыт работы с PostgreSQL или MongoDB\n— Понимание Docker и базовых DevOps-практик",
        ],
        [
            'title' => 'DevOps Engineer',
            'description' => "Ищем DevOps-инженера для поддержки и развития инфраструктуры компании.\n\nОбязанности:\n— Администрирование Kubernetes-кластеров\n— Настройка и поддержка CI/CD (GitHub Actions, GitLab CI)\n— Управление инфраструктурой как кодом (Terraform, Ansible)\n— Мониторинг и алертинг (Grafana, Prometheus)\n\nТребования:\n— Опыт работы с Kubernetes от 2 лет\n— Знание Docker, Terraform\n— Опыт работы с AWS или GCP\n— Опыт настройки CI/CD пайплайнов",
        ],
        [
            'title' => 'Data Engineer',
            'description' => "Ищем Data Engineer для построения и развития data-платформы.\n\nОбязанности:\n— Разработка и поддержка ETL/ELT пайплайнов\n— Работа с большими объёмами данных (Apache Spark)\n— Проектирование хранилищ данных\n— Интеграция с BI-инструментами (Power BI, Tableau)\n\nТребования:\n— Опыт работы с Python и SQL от 3 лет\n— Знание Apache Spark, Kafka\n— Опыт работы с облачными хранилищами (AWS, GCP)\n— Знание Pandas, NumPy",
        ],
        [
            'title' => 'QA Engineer (Automation)',
            'description' => "Ищем QA Automation Engineer для обеспечения качества продуктовой платформы.\n\nОбязанности:\n— Разработка и поддержка автотестов (Playwright, Cypress)\n— Написание API-тестов и интеграционных тестов\n— Настройка тест-фреймворков в CI/CD\n— Улучшение тест-покрытия\n\nТребования:\n— Опыт в автоматизированном тестировании от 2 лет\n— Знание Playwright или Cypress\n— Опыт написания тестов на TypeScript или Python\n— Понимание принципов TDD/BDD",
        ],
        [
            'title' => 'Mobile Developer (Flutter)',
            'description' => "Ищем Mobile-разработчика для создания кроссплатформенного мобильного приложения.\n\nОбязанности:\n— Разработка мобильных приложений на Flutter\n— Интеграция с REST API\n— Написание unit- и widget-тестов\n— Публикация и поддержка в App Create и Google Play\n\nТребования:\n— Опыт разработки на Flutter от 1.5 лет\n— Знание Dart, state management (Riverpod, BLoC)\n— Понимание нативных возможностей iOS и Android\n— Опыт публикации приложений",
        ],
        [
            'title' => 'ML Engineer',
            'description' => "Приглашаем ML Engineer для разработки и внедрения моделей машинного обучения.\n\nОбязанности:\n— Разработка и обучение ML-моделей (TensorFlow, PyTorch)\n— Интеграция моделей в production-среду\n— Feature engineering, работа с большими датасетами\n— Мониторинг качества моделей, A/B-тестирование\n\nТребования:\n— Опыт работы с TensorFlow или PyTorch от 2 лет\n— Уверенное знание Python, Pandas, NumPy\n— Знание Scikit-learn\n— Понимание линейной алгебры и статистики",
        ],
        [
            'title' => 'iOS Developer (Swift)',
            'description' => "Ищем iOS Developer для разработки нативного мобильного приложения.\n\nОбязанности:\n— Разработка iOS-приложения на Swift\n— Интеграция с REST API\n— Написание unit-тестов (XCTest)\n— Оптимизация производительности и памяти\n\nТребования:\n— Опыт разработки iOS на Swift от 2 лет\n— Знание UIKit и/или SwiftUI\n— Понимание архитектурных паттернов (MVVM, VIPER)\n— Опыт публикации в App Create",
        ],
        [
            'title' => 'Android Developer (Kotlin)',
            'description' => "Ищем Android Developer для разработки и поддержки Android-приложения.\n\nОбязанности:\n— Разработка Android-приложения на Kotlin\n— Реализация UI с Jetpack Compose\n— Интеграция с REST API\n— Написание unit- и инструментальных тестов\n\nТребования:\n— Опыт разработки на Kotlin от 2 лет\n— Знание Android SDK, Jetpack-библиотек\n— Опыт работы с MVVM, Clean Architecture\n— Опыт публикации в Google Play",
        ],
        [
            'title' => 'Backend Developer (Python/FastAPI)',
            'description' => "Ищем Python Backend Developer для разработки микросервисов.\n\nОбязанности:\n— Разработка API на FastAPI\n— Проектирование и оптимизация БД (PostgreSQL)\n— Работа с очередями (Kafka, RabbitMQ)\n— Покрытие кода тестами, участие в code review\n\nТребования:\n— Опыт разработки на Python от 2 лет\n— Уверенное знание FastAPI или Django\n— Опыт работы с PostgreSQL, Redis\n— Знание Docker, базовое понимание Kubernetes",
        ],
        [
            'title' => 'Team Lead (Backend)',
            'description' => "Ищем Team Lead Backend для управления командой разработчиков.\n\nОбязанности:\n— Техническое руководство командой из 4-6 разработчиков\n— Декомпозиция задач, планирование спринтов\n— Проведение code review и архитектурных обсуждений\n— Взаимодействие с product manager\n— Найм и онбординг новых разработчиков\n\nТребования:\n— Опыт разработки от 5 лет, из них от 1 года Team Lead\n— Уверенные знания backend-разработки\n— Опыт проектирования распределённых систем\n— Навыки проведения 1:1, умение давать обратную связь\n— Опыт работы по Agile/Scrum",
        ],
    ];

    private static array $locations = [
        'Москва', 'Санкт-Петербург', 'Новосибирск',
        'Екатеринбург', 'Казань', 'Нижний Новгород', 'Remote',
    ];

    public function definition(): array
    {
        $position = fake()->randomElement(self::$positions);
        $salaryMin = fake()->optional(0.8)->numberBetween(100000, 350000);

        return [
            'tenant_id' => Tenant::factory(),
            'title' => $position['title'],
            'description' => $position['description'],
            'employment_type' => fake()->randomElement(EmploymentType::cases())->value,
            'work_mode' => fake()->randomElement(WorkMode::cases())->value,
            'salary_min' => $salaryMin,
            'salary_max' => $salaryMin ? fake()->numberBetween($salaryMin, $salaryMin + 150000) : null,
            'salary_currency' => fake()->randomElement(['RUB', 'USD', 'EUR']),
            'status' => fake()->randomElement(Status::cases())->value,
            'location' => fake()->optional(0.7)->randomElement(self::$locations),
            'published_at' => fake()->optional(0.6)->dateTimeBetween('-6 months', 'now'),
            'closed_at' => null,
            'created_by_id' => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state([
            'status' => Status::PUBLISHED->value,
            'published_at' => now(),
        ]);
    }

    public function forTenant(Tenant $tenant, User $user): static
    {
        return $this->state([
            'tenant_id' => $tenant->id,
            'created_by_id' => $user->id,
        ]);
    }
}
