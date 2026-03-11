<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Skill\Enum\Level;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('skillables', function (Blueprint $table) {
            $table->foreignId('skill_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->morphs('skillable');
            $table->enum('level', Level::values())
                ->default(Level::BEGINNER);
            $table->primary(['skill_id', 'skillable_id', 'skillable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skillables');

        Schema::dropIfExists('skills');
    }
};
