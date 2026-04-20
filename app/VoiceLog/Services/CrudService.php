<?php

declare(strict_types=1);

namespace App\VoiceLog\Services;

use App\VoiceLog\Dto\Create;
use App\VoiceLog\Models\VoiceLog;

final readonly class CrudService
{
    public function create(Create $dto): VoiceLog
    {
        $voiceLog = new VoiceLog($dto->toArray());

        $voiceLog->save();

        return $voiceLog;
    }
}
