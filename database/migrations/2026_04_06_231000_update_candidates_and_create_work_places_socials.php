<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('socials', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->string('name');
            $table->string('url');
            $table->timestamps();
        });

        Schema::create('work_places', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('position');
            $table->text('description')->nullable();
            $table->date('started_at');
            $table->date('ended_at')->nullable();
            $table->timestamps();
        });

        Schema::table('candidates', static function (Blueprint $table) {
            $table->dropColumn(['linkedin_url', 'github_url']);
            // Удаляем grade, если он существует в базе. 
            // Судя по всему, в миграции create_candidates его нет, но он был в модели.
            // Если он был добавлен вручную в БД или в другой миграции, которую я не нашел.
            if (Schema::hasColumn('candidates', 'grade')) {
                $table->dropColumn('grade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', static function (Blueprint $table) {
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('grade')->nullable(); // Возвращаем как строку для простоты отката
        });

        Schema::dropIfExists('work_places');
        Schema::dropIfExists('socials');
    }
};
