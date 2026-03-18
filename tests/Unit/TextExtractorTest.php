<?php

use App\Resume\Services\Extract\TxtExtractor;
use Illuminate\Http\UploadedFile;

it('extracts text file from txt file', function () {
    $file = UploadedFile::fake()->createWithContent(
        'resume.txt',
        'Иван Иванов\nPHP Developer\n5 лет опыта',
    );

    $extractor = new TxtExtractor();
    $result = $extractor->extract($file);

    expect($result)
        ->toBeString()
        ->toContain('Иван Иванов')
        ->toContain('PHP Developer');
});
