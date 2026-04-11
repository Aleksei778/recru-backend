<?php

declare(strict_types=1);

namespace App\Resume\Services;

use App\Candidate\Enum\Source;
use App\Candidate\Enum\Status;
use App\Candidate\Models\Candidate;
use App\Candidate\Models\Social;
use App\Candidate\Models\WorkPlace;
use App\Resume\Dto\ParsedResumeDto;
use App\Resume\Services\Extract\PdfExtractor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final readonly class ResumeManager
{
    public function __construct(
        private PdfExtractor $pdfExtractor,
        private ResumeAiService $aiService,
    ) {
    }

    /**
     * Обработка PDF резюме: парсинг, сохранение/обновление кандидата, AI оценка.
     */
    public function handlePdfUpload(UploadedFile $file, ?int $tenantId = null, ?int $userId = null): array
    {
        $text = $this->pdfExtractor->extract($file);
        
        $parsedDto = $this->aiService->parse($text);
        if (!$parsedDto) {
            throw new \RuntimeException('Failed to parse resume via AI');
        }

        $assessment = $this->aiService->assess($text);

        return DB::transaction(function () use ($parsedDto, $assessment, $tenantId, $userId) {
            $candidate = $this->findOrCreateCandidate($parsedDto, $tenantId, $userId);
            
            // Здесь можно сохранить результаты оценки, например в отдельную таблицу или в поля кандидата
            // Для данного ТЗ предположим, что мы обновляем статус и возвращаем данные
            if ($assessment) {
                $candidate->update([
                    'status' => Status::SCREENED,
                    // Если бы были поля для оценки в БД, мы бы их обновили здесь
                ]);
            }

            return [
                'candidate' => $candidate,
                'parsed_data' => $parsedDto->toArray(),
                'assessment' => $assessment,
            ];
        });
    }

    private function findOrCreateCandidate(ParsedResumeDto $dto, ?int $tenantId, ?int $userId): Candidate
    {
        $candidate = null;

        if ($dto->email) {
            $candidate = Candidate::where('email', $dto->email)->first();
        }

        if (!$candidate && $dto->phone) {
            $candidate = Candidate::where('phone', $dto->phone)->first();
        }

        $data = [
            'first_name' => $dto->first_name,
            'last_name' => $dto->last_name,
            'middle_name' => $dto->middle_name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'experience_years' => $dto->experience_years,
            'education_level' => $dto->education_level,
        ];

        if ($candidate) {
            $candidate->update($data);
        } else {
            $data['tenant_id'] = $tenantId;
            $data['added_by_id'] = $userId;
            $data['source'] = Source::BULK_IMPORT;
            $data['status'] = Status::NEW;

            $candidate = Candidate::create($data);
        }

        $this->syncSocials($candidate, $dto->socials);
        $this->syncWorkPlaces($candidate, $dto->work_places);

        return $candidate;
    }

    private function syncSocials(Candidate $candidate, array $socials): void
    {
        foreach ($socials as $socialData) {
            Social::updateOrCreate(
                ['candidate_id' => $candidate->id, 'name' => $socialData['name']],
                ['url' => $socialData['url']]
            );
        }
    }

    private function syncWorkPlaces(Candidate $candidate, array $workPlaces): void
    {
        // Для простоты удалим старые и добавим новые, либо можно реализовать более сложную логику
        $candidate->workPlaces()->delete();

        foreach ($workPlaces as $wpData) {
            WorkPlace::create([
                'candidate_id' => $candidate->id,
                'company_name' => $wpData['company_name'] ?? 'Unknown',
                'position' => $wpData['position'] ?? 'Unknown',
                'description' => $wpData['description'] ?? null,
                'started_at' => $wpData['started_at'] ?? now()->toDateString(),
                'ended_at' => $wpData['ended_at'] ?? null,
            ]);
        }
    }
}
