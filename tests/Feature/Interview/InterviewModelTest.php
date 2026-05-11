<?php

declare(strict_types=1);

namespace Tests\Feature\Interview;

use App\Interview\Enum\Status;
use App\Interview\Models\Interview;
use Tests\Feature\FeatureTestCase;

final class InterviewModelTest extends FeatureTestCase
{
    public function test_is_pending_returns_true_when_status_is_pending(): void
    {
        $interview = Interview::factory()->create(['status' => Status::Pending]);

        $this->assertTrue($interview->isPending());
        $this->assertFalse($interview->isInProgress());
        $this->assertFalse($interview->isReady());
    }

    public function test_is_ready_returns_true_when_status_is_ready(): void
    {
        $interview = Interview::factory()->create(['status' => Status::Ready]);

        $this->assertTrue($interview->isReady());
        $this->assertFalse($interview->isPending());
    }

    public function test_is_in_progress_returns_true_when_status_is_in_progress(): void
    {
        $interview = Interview::factory()->create(['status' => Status::InProgress]);

        $this->assertTrue($interview->isInProgress());
        $this->assertFalse($interview->isPending());
    }

    public function test_is_processing_returns_true_when_status_is_processing(): void
    {
        $interview = Interview::factory()->create(['status' => Status::Processing]);

        $this->assertTrue($interview->isProcessing());
    }

    public function test_mark_as_in_progress_updates_status(): void
    {
        $interview = Interview::factory()->create(['status' => Status::Ready]);

        $interview->markAsInProgress();

        $this->assertTrue($interview->isInProgress());
        $this->assertDatabaseHas('interviews', [
            'id' => $interview->id,
            'status' => Status::InProgress->value,
        ]);
    }

    public function test_mark_as_processing_updates_status(): void
    {
        $interview = Interview::factory()->create(['status' => Status::InProgress]);

        $interview->markAsProcessing();

        $this->assertTrue($interview->isProcessing());
        $this->assertDatabaseHas('interviews', [
            'id' => $interview->id,
            'status' => Status::Processing->value,
        ]);
    }

    public function test_mark_as_evaluating_updates_status(): void
    {
        $interview = Interview::factory()->create(['status' => Status::Processing]);

        $interview->markAsEvaluating();

        $this->assertDatabaseHas('interviews', [
            'id' => $interview->id,
            'status' => Status::Evaluating->value,
        ]);
    }

    public function test_mark_as_evaluated_updates_status(): void
    {
        $interview = Interview::factory()->create(['status' => Status::Evaluating]);

        $interview->markAsEvaluated();

        $this->assertDatabaseHas('interviews', [
            'id' => $interview->id,
            'status' => Status::Evaluated->value,
        ]);
    }

    public function test_mark_as_closed_updates_status(): void
    {
        $interview = Interview::factory()->evaluated()->create();

        $interview->markAsClosed();

        $this->assertDatabaseHas('interviews', [
            'id' => $interview->id,
            'status' => Status::Closed->value,
        ]);
    }

    public function test_mark_as_questions_review_updates_status(): void
    {
        $interview = Interview::factory()->create(['status' => Status::GeneratingQuestions]);

        $interview->markAsQuestionsReview();

        $this->assertDatabaseHas('interviews', [
            'id' => $interview->id,
            'status' => Status::QuestionsReview->value,
        ]);
    }

    public function test_is_questions_review_returns_true_for_correct_status(): void
    {
        $interview = Interview::factory()->create(['status' => Status::QuestionsReview]);

        $this->assertTrue($interview->isQuestionsReview());
    }

    public function test_status_cast_returns_enum(): void
    {
        $interview = Interview::factory()->create(['status' => Status::Pending]);

        $this->assertInstanceOf(Status::class, $interview->status);
        $this->assertEquals(Status::Pending, $interview->status);
    }
}
