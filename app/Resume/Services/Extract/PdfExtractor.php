<?php

declare(strict_types=1);

namespace App\Resume\Services\Extract;

use Illuminate\Http\UploadedFile;
use Psr\Log\LoggerInterface;
use Smalot\PdfParser\Parser;

final readonly class PdfExtractor implements ExtractorInterface
{
    public function __construct(
        private Parser $parser,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @throws \Exception
     */
    public function extract(UploadedFile $file): string
    {
        try {
            $pdf = $this->parser->parseFile($file->getRealPath());

            return preg_replace('/\s+/', ' ', trim($pdf->getText()));
        } catch (\Exception $exception) {
            $this->logger->error('Error while parsing PDF: ' . $exception->getMessage());

            throw $exception;
        }
    }
}
