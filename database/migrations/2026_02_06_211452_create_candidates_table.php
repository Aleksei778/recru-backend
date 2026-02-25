<?php

declare(strict_types=1);

use App\Job\Enum\SeniorityLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Candidate\Enum\Source;
use App\Candidate\Enum\Status;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();

            $table->string('tenant_id');
            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('added_by_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('resume_url');
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->enum('education_level', SeniorityLevel::values());
            $table->enum('source', Source::values());
            $table->enum('status', Status::values())->default(Status::NEW);
            $table->integer('experience_years');
            $table->integer('match_score')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
