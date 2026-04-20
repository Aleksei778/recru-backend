<?php

declare(strict_types=1);

namespace App\Resume\Http\Controllers;

use App\Common\Enum\Locale;
use App\Common\Http\Controllers\Controller;
use App\Resume\Http\Requests\{StringRequest, FileRequest};
use Illuminate\Http\JsonResponse;
use App\Resume\Services\Extract\{PdfExtractor, TxtExtractor};
use App\Resume\Services\Parser;
use App\Resume\Services\FileUploadService;
use Illuminate\Support\Facades\Auth;

final readonly class ParseResumeController extends Controller
{
    public function __construct(
        private Parser            $parser,
        private PdfExtractor      $pdfExtractor,
        private TxtExtractor      $textExtractor,
        private FileUploadService $resumeManager,
    ) {
    }

    public function parseFile(FileRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $file = $request->file('resume');

        if ($file->getClientMimeType() === 'application/pdf') {
            $result = $this->resumeManager->handlePdfUploadAsync(
                $file,
                Auth::user()?->tenant_id,
                Auth::id()
            );

            return new JsonResponse($result);
        }

        $text = $this->textExtractor->extract($file);

        return $this->respond($text, $validated['locale']);
    }

    public function parseString(StringRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $text = (string) $validated->string('text');

        return $this->respond($text, $validated['locale']);
    }

    private function respond(string $text, Locale $locale): JsonResponse
    {
        try {
            $dto = $this->parser->parse($text, $locale);

            return new JsonResponse($dto->toArray());
        } catch (\JsonException) {
            return response()->json(status: 422);
        }
    }
}
