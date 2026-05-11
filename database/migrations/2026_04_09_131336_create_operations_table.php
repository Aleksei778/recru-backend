<?php

declare(strict_types=1);

use App\Ai\Operation\Enum\{Type, Status};
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
            $table->enum('type', Type::values());
            $table->enum('status', Status::values())->default(Status::Pending);
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
