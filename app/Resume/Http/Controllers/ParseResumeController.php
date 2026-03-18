<?php

declare(strict_types=1);

namespace App\Resume\Http\Controllers;

use App\Base\Enum\Locale;
use App\Base\Http\Controllers\Controller;
use App\Resume\Http\Requests\{StringRequest, FileRequest};
use Illuminate\Http\JsonResponse;
use App\Resume\Services\Extract\{PdfExtractor, TxtExtractor};
use App\Resume\Services\Parser;

final readonly class ParseResumeController extends Controller
{
    public function __construct(
        private Parser       $parser,
        private PdfExtractor $pdfExtractor,
        private TxtExtractor $textExtractor,
    ) {
    }

    public function parseFile(FileRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $file = $validated->file('resume');

        $text = match ($file->getClientMimeType()) {
            'application/pdf' => $this->pdfExtractor->extract($file),
            default => $this->textExtractor->extract($file),
        };

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
