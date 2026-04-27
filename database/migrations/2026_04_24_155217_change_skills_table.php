<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->json('aliases');
            $table->foreignId('category_id')->constrained('skill_categories')->cascadeOnDelete();
            $table->dropColumn('slug');
            $table->string('slug')->unique();
        });
    }

    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('aliases');
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            $table->dropUnique('skills_slug_unique');
            $table->dropColumn('slug');
        });

        Schema::dropIfExists('skill_categories');
    }
};
