<?php

declare(strict_types=1);

namespace App\Ai\Operation\Http\Controllers;

use App\Ai\Operation\Models\Operation;
use App\Ai\Operation\Enum\Status;
use App\Common\Services\JsonDecoder;
use Illuminate\Http\JsonResponse;

final readonly class Controller
{
    public function status(string $subdomain, Operation $operation, JsonDecoder $decoder): JsonResponse
    {
        if ($operation->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json([
            'id' => $operation->id,
            'status' => $operation->status,
            'result' => $operation->status === Status::Completed
                ? $decoder->decodeJson($operation->result)
                : null,
        ]);
    }
}
