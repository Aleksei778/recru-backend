# Recru — Backend

AI-powered recruitment platform backend. Automates the full hiring pipeline: resume parsing, AI-generated interview questions, voice interviews with speech recognition, and automated candidate evaluation — all in a multi-tenant SaaS architecture.

## Stack

- **PHP 8.4** / **Laravel 12**
- **PostgreSQL** — основная БД
- **Redis** — кэш, сессии, очереди
- **Stancl Tenancy** — мультитенантность (изоляция данных по организациям)
- **Yandex Cloud** — GPT (генерация вопросов, оценка интервью, парсинг резюме), STT (распознавание голоса), TTS (синтез вопросов), Object Storage
- **Resend** — транзакционные письма
- **AWS S3** — хранение файлов

## Архитектура

Приложение построено по модульному принципу. Каждый модуль содержит свои Models, Services, Repositories, HTTP-слой и DTO.

```
app/
├── Ai/           — GPT, STT, TTS провайдеры и async-операции
├── Candidate/    — профили кандидатов
├── Email/        — входящие/исходящие письма
├── Interview/    — интервью, вопросы, ответы, оценка
├── Resume/       — парсинг и хранение резюме
├── Skill/        — справочник навыков
├── Tenant/       — мультитенантность
├── User/         — HR-пользователи, аутентификация
├── Vacancy/      — вакансии
└── VoiceLog/     — аудиозаписи ответов
```

### Флоу интервью

```
Создание интервью
      ↓
GeneratingQuestions  — Yandex GPT генерирует вопросы
      ↓
QuestionsReview      — HR проверяет и редактирует вопросы
      ↓
Synthesizing         — Yandex TTS озвучивает каждый вопрос
      ↓
Ready                — кандидат получает ссылку
      ↓
InProgress           — кандидат отвечает голосом
      ↓
Processing           — Yandex STT расшифровывает ответы
      ↓
Evaluating           — Yandex GPT оценивает кандидата
      ↓
Evaluated            — HR видит результат
```

Все долгие AI-операции асинхронны: задача отправляется в Yandex, создаётся запись `Operation`, `CheckOperationJob` поллит статус и обрабатывает результат.

## Установка

### Требования

- PHP 8.4+
- PostgreSQL 15+
- Redis 7+
- Composer

### Локальный запуск

```bash
git clone <repo>
cd recru-backend

composer install

cp .env.example .env
php artisan key:generate
```

Заполните `.env` (минимум для запуска):

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_DATABASE=recru
DB_USERNAME=postgres
DB_PASSWORD=secret

REDIS_HOST=127.0.0.1

CENTRAL_DOMAIN=localhost

YANDEX_FOLDER_ID=
YANDEX_API_KEY=

RESEND_KEY=
```

```bash
php artisan migrate
php artisan db:seed

# Запустить воркер очередей (обязательно для AI-операций)
php artisan queue:work
```

### Docker (Laravel Sail)

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan queue:work
```

## Переменные окружения

| Переменная | Описание |
|---|---|
| `YANDEX_FOLDER_ID` | ID папки в Yandex Cloud |
| `YANDEX_API_KEY` | API-ключ Yandex Cloud (GPT, STT, TTS) |
| `YANDEX_OBJECT_STORAGE_*` | Доступ к Yandex Object Storage |
| `RESEND_KEY` | API-ключ Resend для отправки писем |
| `CENTRAL_DOMAIN` | Домен центрального приложения |
| `AWS_*` | AWS S3 для файлов (опционально) |
| `GIGACHAT_*` | GigaChat как альтернативный LLM (опционально) |
| `SLACK_BOT_USER_OAUTH_TOKEN` | Slack-нотификации (опционально) |

## API

Все tenant-маршруты требуют заголовок `Authorization: Bearer <token>`.

### HR (аутентифицированные)

| Метод | URL | Описание |
|---|---|---|
| `GET` | `/api/hr/interviews` | Список интервью |
| `POST` | `/api/hr/interviews` | Создать интервью |
| `GET` | `/api/hr/interviews/{id}` | Детали интервью |
| `PUT` | `/api/hr/interviews/{id}/questions` | Обновить вопросы |
| `POST` | `/api/hr/interviews/{id}/questions/approve` | Утвердить вопросы |
| `POST` | `/api/hr/interviews/{id}/close` | Закрыть интервью |
| `GET` | `/api/candidates` | Список кандидатов |
| `POST` | `/api/candidates` | Создать кандидата |
| `GET` | `/api/vacancies` | Список вакансий |
| `POST` | `/api/vacancies` | Создать вакансию |
| `POST` | `/api/resume/parse/file` | Распарсить резюме из файла |
| `POST` | `/api/resume/parse/string` | Распарсить резюме из текста |
| `GET` | `/api/emails/inbox` | Входящие письма |
| `POST` | `/api/emails/send` | Отправить письмо |

### Кандидат (по токену, без авторизации)

| Метод | URL | Описание |
|---|---|---|
| `GET` | `/api/candidate/interviews/{token}/questions/next` | Следующий вопрос |
| `POST` | `/api/candidate/interviews/{token}/questions/{id}/answer` | Отправить ответ (аудио) |

## Тесты

```bash
php artisan test
# или
./vendor/bin/pest
```

## Мультитенантность

Каждая организация — отдельный tenant. Данные полностью изолированы. Tenant определяется по поддомену запроса. Центральные маршруты (регистрация, логин) доступны на `CENTRAL_DOMAIN`.