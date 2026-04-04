<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Services;

use App\Ai\Yandex\Dto\ObjectStorage as ObjectStorageDto;
use Aws\S3\S3Client;

final readonly class ObjectStorageService
{
    private string $bucket;
    private S3Client $s3Client;

    public function __construct()
    {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => 'ru-central1',
            'endpoint' => 'https://storage.yandexcloud.net',
            'credentials' => [
                'key' => config('services.yandex.object_storage.key_id'),
                'secret' => config('services.yandex.object_storage.secret'),
            ],
            'use_path_style_endpoint' => false,
        ]);

        $this->bucket = config('services.yandex.object_storage.bucket');
    }

    public function uploadFile(ObjectStorageDto $storageDto): string
    {
        $ext = explode('/', $storageDto->mimeType)[1];
        $key = 'voices/' . date('Y/m/d') . $storageDto->fileId . ".$ext";

        $this->s3Client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'Body' => $storageDto->content,
            'ContentType' => $storageDto->mimeType,
            'Metadata' => [
                'file_id' => $storageDto->fileId,
                'original_size' => $storageDto->fileSize,
                'upload_timestamp' => time(),
            ],
        ]);

        return $key;
    }

    public function getObjectUri(string $key): string
    {
        $command = $this->s3Client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);
        $presignedRequest = $this->s3Client->createPresignedRequest($command, '+1 hour');

        return (string)$presignedRequest->getUri();
    }
}
