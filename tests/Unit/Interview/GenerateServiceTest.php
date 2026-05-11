<?php

declare(strict_types=1);

namespace Tests\Unit\Interview;

use App\Ai\Gpt\Prompts\Interview\QuestionsGenerator;
use App\Ai\Gpt\Providers\GptInterface;
use App\Ai\Operation\Services\CrudService as OperationCrudService;
use App\Interview\Models\Interview;
use App\Interview\Services\Questions\CrudService as QuestionsCrudService;
use App\Interview\Services\Questions\GenerateService;
use Illuminate\Support\Facades\DB;
use Mockery;
use Psr\Log\LoggerInterface;
use Tests\Unit\UnitTestCase;

final class GenerateServiceTest extends UnitTestCase
{
    public function test_generate_returns_false_and_logs_when_gpt_returns_null(): void
    {
        $interview = new Interview();
        $interview->forceFill(['id' => 5]);

        $generator = Mockery::mock(QuestionsGenerator::class);
        $generator->allows('messages')->with($interview)->andReturn([]);

        $gpt = Mockery::mock(GptInterface::class);
        $gpt->allows('completion')->with([])->andReturn(null);

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->expects('error')
            ->once()
            ->with('Failed to submit question generation', ['interview_id' => 5]);

        $service = new GenerateService(
            questionsGenerator: $generator,
            gptService: $gpt,
            crudService: Mockery::mock(QuestionsCrudService::class),
            logger: $logger,
            operationCrudService: Mockery::mock(OperationCrudService::class),
        );

        $this->assertFalse($service->generate($interview));
    }

    public function test_handle_generation_result_creates_one_question_per_nonempty_line(): void
    {
        $interview = Mockery::mock(Interview::class);
        $interview->allows('markAsQuestionsReview');

        $questionsCrud = Mockery::mock(QuestionsCrudService::class);
        $questionsCrud->expects('create')->once()->with($interview, 'What is PHP?', 1);
        $questionsCrud->expects('create')->once()->with($interview, 'Explain OOP', 2);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(fn($callback) => $callback());

        $service = new GenerateService(
            questionsGenerator: Mockery::mock(QuestionsGenerator::class),
            gptService: Mockery::mock(GptInterface::class),
            crudService: $questionsCrud,
            logger: Mockery::mock(LoggerInterface::class),
            operationCrudService: Mockery::mock(OperationCrudService::class),
        );

        $service->handleGenerationResult($interview, "What is PHP?\nExplain OOP");
    }
}
