<?php

declare(strict_types=1);

namespace Tests\Unit\Interview;

use App\Ai\Gpt\Prompts\Interview\EvaluationGenerator;
use App\Ai\Gpt\Providers\GptInterface;
use App\Ai\Operation\Jobs\CheckOperationJob;
use App\Ai\Operation\Models\Operation;
use App\Ai\Operation\Services\CrudService as OperationCrudService;
use App\Interview\Models\Interview;
use App\Interview\Services\EvaluationService;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Psr\Log\LoggerInterface;
use Tests\Unit\UnitTestCase;

final class EvaluationServiceTest extends UnitTestCase
{
    public function test_evaluate_returns_false_and_logs_when_gpt_returns_null(): void
    {
        $interview = new Interview();
        $interview->forceFill(['id' => 42]);

        $generator = Mockery::mock(EvaluationGenerator::class);
        $generator->allows('messages')->with($interview)->andReturn([]);

        $gpt = Mockery::mock(GptInterface::class);
        $gpt->allows('completion')->with([])->andReturn(null);

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects('error')
            ->once()
            ->with('Failed to submit evaluation', ['interview_id' => 42]);

        $service = new EvaluationService(
            evaluationGenerator: $generator,
            gptService: $gpt,
            operationCrudService: Mockery::mock(OperationCrudService::class),
            logger: $logger,
        );

        $this->assertFalse($service->evaluate($interview));
    }

    public function test_evaluate_returns_true_and_queues_check_job_when_gpt_succeeds(): void
    {
        Queue::fake();
        config(['ai.provider' => 'openai']);

        $interview = new Interview();
        $interview->forceFill(['id' => 7]);

        $generator = Mockery::mock(EvaluationGenerator::class);
        $generator->allows('messages')->andReturn([]);

        $gpt = Mockery::mock(GptInterface::class);
        $gpt->allows('completion')->andReturn('ext-job-id-999');

        $mockOperation = (new Operation())->forceFill(['id' => 1]);

        $operationService = Mockery::mock(OperationCrudService::class);
        $operationService->expects('create')->once()->andReturn($mockOperation);

        $service = new EvaluationService(
            evaluationGenerator: $generator,
            gptService: $gpt,
            operationCrudService: $operationService,
            logger: Mockery::mock(LoggerInterface::class),
        );

        $result = $service->evaluate($interview);

        $this->assertTrue($result);
        Queue::assertPushed(CheckOperationJob::class);
    }

    public function test_handle_evaluation_result_logs_error_when_response_is_not_valid_json(): void
    {
        $interview = new Interview();
        $interview->forceFill(['id' => 3]);

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects('error')
            ->once()
            ->with('Failed to decode evaluation result', ['interview_id' => 3]);

        $service = new EvaluationService(
            evaluationGenerator: Mockery::mock(EvaluationGenerator::class),
            gptService: Mockery::mock(GptInterface::class),
            operationCrudService: Mockery::mock(OperationCrudService::class),
            logger: $logger,
        );

        $service->handleEvaluationResult($interview, 'not json at all');
    }
}
