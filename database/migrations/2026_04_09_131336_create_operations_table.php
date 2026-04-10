<?php

declare(strict_types=1);

use App\Ai\Yandex\Enum\{OperationType, OperationStatus};
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->enum('type', OperationType::values());
            $table->enum('status', OperationStatus::values())->default(OperationStatus::PENDING);
            $table->string('yandex_id')->nullable()->index();
            $table->json('raw_response')->nullable();
            $table->json('raw_request')->nullable();
            $table->json('result')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
