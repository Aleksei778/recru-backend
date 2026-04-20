<?php

declare(strict_types=1);

namespace App\Resume\Services;

use App\Ai\Gpt\Prompts\Resume\EvalGenerator;
use App\Ai\Yandex\Dto\ObjectStorage as ObjectStorageDto;
use App\Ai\Yandex\Services\ObjectStorageService;
use App\Candidate\Enum\Status;
use App\Candidate\Repositories\CandidateRepository;
use App\Resume\Models\Resume;
use App\Resume\Services\Extract\PdfExtractor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final readonly class FileUploadService
{
    public function __construct(
        private PdfExtractor $pdfExtractor,
        private EvalGenerator $evaluationGenerator,
        private CandidateRepository $candidateRepository,
        private ObjectStorageService $objectStorageService,
    ) {
    }

    public function handlePdfUploadAsync(UploadedFile $file, ?int $tenantId = null, ?int $userId = null): array
    {
        $text = $this->pdfExtractor->extract($file);

        $dto = new ObjectStorageDto(
            content: $file->getContent(),
            mimeType: $file->getClientMimeType(),
            fileId: bin2hex(random_bytes(8)),
            fileSize: (string) $file->getSize()
        );
        $path = $this->objectStorageService->uploadFile($dto, 'resumes');

        return DB::transaction(function () use ($text, $path, $file, $tenantId, $userId) {
            // 2. Ищем или создаем временного кандидата
            $candidate = $this->findOrCreatePendingCandidate($tenantId, $userId);

            // 3. Создаем запись резюме
            $resume = Resume::create([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'storage_disk' => 'yandex_object_storage',
            ]);

            // 4. Запускаем асинхронный парсинг
            $parseOpId = $this->aiService->parse($text, $resume);

            // 5. Запускаем асинхронную оценку
            $evalOpId = $this->aiService->evaluate($text, $resume);

            return [
                'candidate_id' => $candidate->id,
                'resume_id' => $resume->id,
                'parse_operation_id' => $parseOpId,
                'evaluation_operation_id' => $evalOpId,
                'status' => 'processing',
            ];
        });
    }

    /**
     * Обработка PDF резюме: парсинг, сохранение/обновление кандидата, AI оценка.
     */
    public function handlePdfUpload(UploadedFile $file, ?int $tenantId = null, ?int $userId = null): array
    {
        $text = $this->pdfExtractor->extract($file);
        
        $parsedDto = $this->aiService->syncParse($text);
        if (!$parsedDto) {
            throw new \RuntimeException('Failed to parse resume via AI');
        }

        $assessment = $this->aiService->syncEvaluate($text);

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
}
