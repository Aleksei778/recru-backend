<?php

declare(strict_types=1);

namespace App\Resume\Services;

use App\Ai\Exceptions\ChatException;
use App\Ai\Services\GigaChatService;
use App\Base\Enum\Locale;
use App\Resume\Dto\ParsedResumeDto;

final readonly class Parser
{
    public function __construct(
        private readonly GigaChatService $gigaChat,
    ) {
    }

    /**
     * @throws ChatException
     * @throws \JsonException
     */
    public function parse(string $resumeText, Locale $locale): ParsedResumeDto
    {
        $response = $this->gigaChat->chat([
            [
                'role' => 'system',
                'content' => config("prompts.$locale->value.prompts.ru.parsing.system_prompt"),
            ],
            [
                'role' => 'user',
                'content' => "Resume:\n\n$resumeText",
            ],
        ]);

        $data = json_decode($response, true, flags: JSON_THROW_ON_ERROR);

        return new ParsedResumeDTO(
            first_name: $data['first_name'] ?? '',
            last_name: $data['last_name'] ?? '',
            middle_name: $data['middle_name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            linkedin_url: $data['linkedin_url'] ?? null,
            github_url: $data['github_url'] ?? null,
            experience_years: isset($data['experience_years']) ? (int) $data['experience_years'] : null,
            grade: $data['grade'] ?? null,
            education_level: $data['education_level'] ?? null,
            skills: $data['skills'] ?? [],
            summary: $data['summary'] ?? null,
        );
    }
}
