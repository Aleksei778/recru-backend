<?php

declare(strict_types=1);

namespace App\Ai\Operation\Http\Controllers;

use App\Ai\Operation\Models\Operation;
use App\Ai\Operation\Enum\Status;
use Illuminate\Http\JsonResponse;

final readonly class Controller
{
    public function status(Operation $operation): JsonResponse
    {
        if ($operation->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json([
            'id' => $operation->id,
            'status' => $operation->status,
            'result' => $operation->status === Status::Completed
                ? json_decode($operation->result, true)
                : null,
        ]);
    }
}
