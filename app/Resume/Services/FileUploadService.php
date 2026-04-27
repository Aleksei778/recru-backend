<?php

declare(strict_types=1);

namespace App\Resume\Services;

use App\Common\Services\Storage;
use App\Resume\Dto\Create;
use App\Resume\Services\Extract\{PdfExtractor, TxtExtractor};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final readonly class FileUploadService
{
    public function __construct(
        private PdfExtractor $pdfExtractor,
        private TxtExtractor $txtExtractor,
        private Storage $storage,
        private ParseService $parseService,
        private EvaluationService $evaluationService,
        private CrudService $crudService,
    ) {
    }

    public function handleFileUpload(
        UploadedFile $file,
        int $tenantId,
        int $savedById,
    ): array {
        $text = match ($file->getClientMimeType()) {
            'application/pdf' => $this->pdfExtractor->extract($file),
            'text/plain' => $this->txtExtractor->extract($file),
            default => throw new \InvalidArgumentException('Unsupported file type'),
        };

        $path = "tenant/$tenantId/resumes/" . $file->getClientOriginalName();

        $this->storage->put(
            disk: config('app.storage_provider'),
            path: $path,
            content: $file->getContent()
        );

        return DB::transaction(function () use ($text, $path, $file, $savedById) {
            $resume = $this->crudService->create(
                new Create(
                    filePath: $path,
                    fileName: $file->getClientOriginalName(),
                    mimeType: $file->getClientMimeType(),
                    size: $file->getSize(),
                    storageDisk: config('app.storage_provider'),
                    savedById: $savedById
                )
            );

            $parseOpId = $this->parseService->parse($text, $resume);
            $evalOpId = $this->evaluationService->evaluate($text, $resume);

            return [
                'resume_id' => $resume->id,
                'parse_operation_id' => $parseOpId,
                'evaluate_operation_id' => $evalOpId,
            ];
        });
    }
}
