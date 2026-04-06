<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Vacancy\Enum\{Status, WorkMode, EmploymentType};

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('created_by_id')->constrained('users')->restrictOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('employment_type', EmploymentType::values())->default(EmploymentType::FULL_TIME);
            $table->string('work_mode')->nullable();
            $table->unsignedInteger('salary_min')->nullable();
            $table->unsignedInteger('salary_max')->nullable();
            $table->char('salary_currency', 3)->nullable()->comment('ISO 4217, e.g. RUB, USD');
            $table->enum('status', Status::values())->default(Status::DRAFT);
            $table->string('location')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->index('tenant_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
