<?php

declare(strict_types=1);

namespace App\Ai\Operation\Http\Controllers;

use App\Ai\Operation\Models\Operation;
use App\Ai\Yandex\Enum\OperationStatus;
use Illuminate\Http\JsonResponse;

final readonly class Controller
{
    public function status(Operation $operation): JsonResponse
    {
        return response()->json([
            'id' => $operation->id,
            'status' => $operation->status,
            'result' => $operation->status === OperationStatus::COMPLETED
                ? json_decode($operation->result, true)
                : null,
        ]);
    }
}
