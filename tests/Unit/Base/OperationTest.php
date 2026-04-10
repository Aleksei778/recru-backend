<?php

declare(strict_types=1);

namespace Tests\Unit\Base;

use App\Ai\Yandex\Enum\{OperationStatus, OperationType};
use App\Ai\Yandex\Models\Operation;
use App\Interview\Models\Interview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_operation(): void
    {
        $interview = Interview::factory()->create();

        $operation = Operation::create([
            'interview_id' => $interview->id,
            'type' => OperationType::STT,
            'status' => OperationStatus::PENDING,
            'external_id' => 'op-123',
            'metadata' => ['key' => 'value'],
        ]);

        $this->assertDatabaseHas('operations', [
            'id' => $operation->id,
            'external_id' => 'op-123',
            'type' => 'stt',
            'status' => 'pending',
        ]);
        
        $this->assertEquals(['key' => 'value'], $operation->metadata);
    }
}
